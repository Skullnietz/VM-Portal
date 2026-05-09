<?php

namespace App\Console\Commands;

use App\Mail\FalloSincronizacionMail;
use App\Mail\MaquinaDesactualizadaUsuarioMail;
use App\Mail\ReporteEmpleadosMail;
use App\Services\ReporteConsumoService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EnviarReportesProgramados extends Command
{
    protected $signature = 'reportes:enviar
                            {--frecuencia= : Procesar solo esta frecuencia (diario|semanal|mensual)}
                            {--planta=    : Procesar solo la planta con este Id_Planta}
                            {--test       : Modo prueba: genera el Excel pero no envía emails}';

    protected $description = 'Envía reportes de consumos según la configuración de alertas de cada usuario';

    protected $reporteService;

    public function __construct(ReporteConsumoService $reporteService)
    {
        parent::__construct();
        $this->reporteService = $reporteService;
    }

    public function handle(): int
    {
        $hoy        = Carbon::now();
        $diaSemana  = $hoy->dayOfWeekIso;
        $diaMes     = $hoy->day;
        $soloPlanta = $this->option('planta') ? (int) $this->option('planta') : null;
        $test       = (bool) $this->option('test');

        if ($test) {
            $this->warn('⚙️  Modo prueba activo — no se enviarán emails.');
        }

        // ── Alerta global: máquinas sin sincronización por planta ────────────
        $plantasQuery = DB::table('Cat_Plantas')->where('Txt_Estatus', 'Alta');
        if ($soloPlanta) {
            $plantasQuery->where('Id_Planta', $soloPlanta);
        }

        foreach ($plantasQuery->get() as $planta) {
            $plantaId    = $planta->Id_Planta;
            $nombrePlanta = $planta->Txt_Nombre_Planta;

            $todas = DB::table('Ctrl_Mquinas')
                ->where('Id_Planta', $plantaId)
                ->where('Txt_Estatus', 'Alta')
                ->pluck('Id_Maquina')
                ->toArray();

            $sincronizadas = collect(DB::select('SET NOCOUNT ON;EXEC SP_Consulta_Sincronizacion_x_Planta ?', [$plantaId]))
                ->pluck('Id_Maquina')
                ->toArray();

            $faltantes = array_diff($todas, $sincronizadas);

            if (count($faltantes) > 0) {
                $maquinasFaltantes = DB::table('Ctrl_Mquinas')->whereIn('Id_Maquina', $faltantes)->get();
                $adminEmails       = DB::table('Cat_Usuarios_Administradores')->whereNotNull('Email')->pluck('Email');

                if (!$test) {
                    foreach ($adminEmails as $correo) {
                        Mail::to($correo)->send(new FalloSincronizacionMail($maquinasFaltantes, $nombrePlanta));
                    }
                }

                $this->warn("❌ Planta {$plantaId} ({$nombrePlanta}): máquinas sin sincronizar.");
            }
        }

        // ── Reportes por usuario ─────────────────────────────────────────────
        $query = DB::table('Configuracion_Reportes')->where('Activo', 1);

        if ($this->option('frecuencia')) {
            $query->where('Frecuencia', $this->option('frecuencia'));
        }

        foreach ($query->get() as $config) {
            if (!$this->debeProcesar($config->Frecuencia, $diaSemana, $diaMes)) {
                continue;
            }

            $this->info("📤 Evaluando usuario ID: {$config->Id_Usuario}");

            if (!$config->Email) {
                $this->warn("⚠️  Usuario {$config->Id_Usuario}: sin email configurado.");
                continue;
            }

            $usuario = DB::table('Cat_Usuarios')->where('Id_Usuario', $config->Id_Usuario)->first();
            $idPlanta = $usuario->Id_Planta ?? null;

            if (!$idPlanta) {
                $this->warn("⚠️  Usuario {$config->Id_Usuario}: no tiene planta asignada.");
                continue;
            }

            if ($soloPlanta && $idPlanta !== $soloPlanta) {
                continue;
            }

            [$start, $end] = $this->getRangoFechas($config->Frecuencia);

            if (!$start || !$end) {
                $this->info("📭 Omitido (frecuencia {$config->Frecuencia}, día no aplica).");
                continue;
            }

            $desactualizadas = $this->maquinasDesactualizadas($idPlanta, $end);

            if ($desactualizadas->isNotEmpty()) {
                $this->warn("❌ Usuario {$config->Id_Usuario}: máquinas desactualizadas, no se envía el reporte.");

                if (!$test) {
                    Mail::to($config->Email)->send(new MaquinaDesactualizadaUsuarioMail($desactualizadas, $start, $end));

                    $adminEmails = DB::table('Cat_Usuarios_Administradores')->whereNotNull('Email')->pluck('Email');
                    foreach ($adminEmails as $adminCorreo) {
                        Mail::to($adminCorreo)->send(new MaquinaDesactualizadaUsuarioMail($desactualizadas, $start, $end));
                    }

                    $now = Carbon::now();
                    foreach ($desactualizadas as $maquina) {
                        DB::table('vending_notifications')->insert([
                            'Id_Planta'   => $maquina->Id_Planta,
                            'Id_Maquina'  => $maquina->Id_Maquina,
                            'Txt_Nombre'  => $maquina->Txt_Nombre,
                            'Txt_Estatus' => $maquina->Txt_Estatus,
                            'description' => 'La máquina no tiene sincronización reciente para generar el reporte.',
                            'Fecha'       => $now,
                            'Fecha_Reg'   => $now,
                            'read_at'     => null,
                            'User_Id'     => $usuario->Id_Usuario,
                        ]);
                    }
                }

                continue;
            }

            // Resolve plant-specific or general report strategy
            $reporte  = $this->reporteService->resolver($config->Plantilla ?? null);
            $filename = $reporte->generar($start, $end, $idPlanta, $config->Frecuencia);

            $this->info("✅ Excel generado: {$filename} via [{$reporte->getNombre()}]");

            if (!$test) {
                Mail::to($config->Email)->send(new ReporteEmpleadosMail(
                    "Este reporte cubre el periodo del {$start->format('d/m/Y')} al {$end->format('d/m/Y')}.",
                    $filename,
                    $start,
                    $end
                ));
            }

            DB::table('Configuracion_Reportes')
                ->where('Id', $config->Id)
                ->update(['Ultimo_Envio' => Carbon::now(), 'updated_at' => Carbon::now()]);
        }

        return Command::SUCCESS;
    }

    private function debeProcesar($frecuencia, $diaSemana, $diaMes)
    {
        if ($frecuencia === 'diario') return true;
        if ($frecuencia === 'semanal') return $diaSemana === 1;
        if ($frecuencia === 'mensual') return $diaMes === 1;
        return false;
    }

    private function getRangoFechas(string $frecuencia): array
    {
        switch ($frecuencia) {
            case 'diario':
                $dia = Carbon::now()->dayOfWeekIso;
                if ($dia === 1) {
                    return [Carbon::now()->subDays(3)->startOfDay(), Carbon::now()->subDay()->endOfDay()];
                }
                if (in_array($dia, [6, 7])) {
                    return [null, null];
                }
                return [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()];

            case 'semanal':
                return [
                    Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY),
                    Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY),
                ];

            case 'mensual':
                return [
                    Carbon::now()->subMonthNoOverflow()->startOfMonth(),
                    Carbon::now()->subMonthNoOverflow()->endOfMonth(),
                ];

            default:
                return [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()];
        }
    }

    private function maquinasDesactualizadas(int $idPlanta, Carbon $hasta)
    {
        $todasMaquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlanta)
            ->where('Txt_Estatus', 'Alta')
            ->get()
            ->keyBy('Id_Maquina');

        $sincronizadas = collect(DB::select('SET NOCOUNT ON;EXEC SP_Consulta_Sincronizacion_x_Planta ?', [$idPlanta]))
            ->keyBy('Id_Maquina');

        $desactualizadas = [];

        foreach ($todasMaquinas as $id => $maquina) {
            if (!isset($sincronizadas[$id])) {
                $maquina->Ultima_Sincronizacion = null;
                $desactualizadas[] = $maquina;
            } else {
                $ultima = Carbon::parse($sincronizadas[$id]->Ultima_Sincronizacion);
                if ($ultima->lt($hasta)) {
                    $maquina->Ultima_Sincronizacion = $ultima;
                    $desactualizadas[] = $maquina;
                }
            }
        }

        return collect($desactualizadas);
    }
}
