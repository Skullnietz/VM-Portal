<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteEmpleadosMail;
use App\Mail\FalloSincronizacionMail;
use App\Mail\MaquinaDesactualizadaUsuarioMail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsumoxEmpleadoExport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnviarReportesProgramados extends Command
{
    protected $signature = 'reportes:enviar';
    protected $description = 'Envía reportes de empleados según configuración del usuario';

    public function handle()
    {
        $hoy = Carbon::now();
        $diaSemana = $hoy->dayOfWeekIso;
        $diaMes = $hoy->day;

        // Validar sincronización por planta (alerta general a admins)
        $plantas = DB::table('Cat_Plantas')->where('Txt_Estatus', 'Alta')->get();

        foreach ($plantas as $planta) {
            $plantaId = $planta->Id_Planta;
            $nombrePlanta = $planta->Txt_Nombre_Planta;

            $todas = DB::table('Ctrl_Mquinas')
                ->where('Id_Planta', $plantaId)
                ->where('Txt_Estatus', 'Alta')
                ->pluck('Id_Maquina')
                ->toArray();

            $sincronizadas = collect(DB::select("SET NOCOUNT ON;EXEC SP_Consulta_Sincronizacion_x_Planta ?", [$plantaId]))
                ->pluck('Id_Maquina')
                ->toArray();

            $faltantes = array_diff($todas, $sincronizadas);

            if (count($faltantes) > 0) {
                $maquinasFaltantes = DB::table('Ctrl_Mquinas')
                    ->whereIn('Id_Maquina', $faltantes)
                    ->get();

                $adminEmails = DB::table('Cat_Usuarios_Administradores')
                    ->whereNotNull('Email')
                    ->pluck('Email');

                foreach ($adminEmails as $correo) {
                    Mail::to($correo)->send(new FalloSincronizacionMail($maquinasFaltantes, $nombrePlanta));
                }

                $this->warn("❌ Planta $plantaId tiene máquinas sin sincronizar. Se notificó a los administradores.");
            }
        }

        // Evaluar reportes por usuario
        $usuarios = DB::table('Configuracion_Reportes')->get();

        foreach ($usuarios as $config) {
            $debeEnviar = false;

            if ($config->Frecuencia === 'diario') {
                $debeEnviar = true;
            } elseif ($config->Frecuencia === 'semanal' && $diaSemana === 1) {
                $debeEnviar = true;
            } elseif ($config->Frecuencia === 'mensual' && $diaMes === 1) {
                $debeEnviar = true;
            }

            if ($debeEnviar) {
                $this->info("📤 Evaluando envío para usuario ID: {$config->Id_Usuario}");

                if ($config->Email) {
                    $usuario = DB::table('Cat_Usuarios')->where('Id_Usuario', $config->Id_Usuario)->first();
                    $idPlanta = $usuario ? $usuario->Id_Planta : null;

                    if (!$idPlanta) {
                        $this->warn("⚠️ No se encontró planta para el usuario {$config->Id_Usuario}");
                        continue;
                    }

                    [$start, $end] = $this->getRangoFechas($config->Frecuencia);

                    if (!$start || !$end) {
                        $this->info("📭 Reporte omitido para usuario ID: {$config->Id_Usuario} (frecuencia: {$config->Frecuencia}, día no válido)");
                        continue;
                    }

                    $desactualizadas = $this->maquinasDesactualizadas($idPlanta, $end);

                    if (count($desactualizadas) > 0) {
                        $this->warn("❌ Usuario {$config->Id_Usuario}: máquinas desactualizadas, no se envía el reporte.");

                        // Notificar por correo al usuario
                        Mail::to($config->Email)->send(new MaquinaDesactualizadaUsuarioMail($desactualizadas, $start, $end));

                        // Notificar a administradores
                        $adminEmails = DB::table('Cat_Usuarios_Administradores')
                            ->whereNotNull('Email')
                            ->pluck('Email');

                        foreach ($adminEmails as $adminCorreo) {
                            Mail::to($adminCorreo)->send(new MaquinaDesactualizadaUsuarioMail($desactualizadas, $start, $end));
                        }

                        // Registrar notificación por cada máquina
                        $currentDateTime = Carbon::now();

                        foreach ($desactualizadas as $maquina) {
                            DB::table('vending_notifications')->insert([
                                'Id_Planta'     => $maquina->Id_Planta,
                                'Id_Maquina'    => $maquina->Id_Maquina,
                                'Txt_Nombre'    => $maquina->Txt_Nombre,
                                'Txt_Estatus'   => $maquina->Txt_Estatus,
                                'description'   => 'La máquina no tiene sincronización reciente para generar el reporte.',
                                'Fecha'         => $currentDateTime,
                                'Fecha_Reg'     => $currentDateTime,
                                'read_at'       => null,
                                'User_Id'       => $usuario->Id_Usuario,
                            ]);
                        }

                        continue;
                    }

                    // Si pasa validación, generar y enviar el reporte
                    $filename = $this->generarExcelDesdeRango($idPlanta, $start, $end, $config->Frecuencia);

                    Mail::to($config->Email)->send(new ReporteEmpleadosMail(
                        "Este reporte cubre el periodo del {$start->format('d/m/Y')} al {$end->format('d/m/Y')}.",
                        $filename,
                        $start,
                        $end
                    ));
                } else {
                    $this->warn("⚠️ El usuario {$config->Id_Usuario} no tiene correo configurado.");
                }
            }
        }

        return Command::SUCCESS;
    }

    private function getRangoFechas($frecuencia)
    {
        switch ($frecuencia) {
            case 'diario':
                $hoy = Carbon::now();
                $dia = $hoy->dayOfWeekIso; // 1 = lunes, 7 = domingo

                if ($dia === 1) {
                    // Lunes → enviar viernes, sábado y domingo
                    $start = Carbon::now()->subDays(3)->startOfDay(); // viernes
                    $end = Carbon::now()->subDay()->endOfDay();       // domingo
                } elseif (in_array($dia, [6, 7])) {
                    // Sábado y domingo → no se envía
                    return [null, null];
                } else {
                    // Martes a viernes → día anterior
                    $start = Carbon::yesterday()->startOfDay();
                    $end = Carbon::yesterday()->endOfDay();
                }
                break;

            case 'semanal':
                $start = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
                $end = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
                break;

            case 'mensual':
                $start = Carbon::now()->subMonthNoOverflow()->startOfMonth();
                $end = Carbon::now()->subMonthNoOverflow()->endOfMonth();
                break;

            default:
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;
        }

        return [$start, $end];
    }


    private function maquinasDesactualizadas($idPlanta, Carbon $hasta)
    {
        // Todas las máquinas activas de la planta
        $todasMaquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlanta)
            ->where('Txt_Estatus', 'Alta')
            ->get()
            ->keyBy('Id_Maquina');

        // Datos del SP con última sincronización
        $sincronizadas = collect(DB::select("SET NOCOUNT ON;EXEC SP_Consulta_Sincronizacion_x_Planta ?", [$idPlanta]))
            ->keyBy('Id_Maquina');

        $desactualizadas = [];

        foreach ($todasMaquinas as $id => $maquina) {
            if (!isset($sincronizadas[$id])) {
                // No ha sincronizado en absoluto
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


    private function generarExcelDesdeRango($idPlanta, Carbon $start, Carbon $end, $frecuencia)
    {
        $fakeRequest = new Request([
            'area' => [],
            'product' => [],
            'employee' => [],
            'dateRange' => $start->format('Y-m-d') . ' - ' . $end->format('Y-m-d'),
        ]);

        $filename = 'reporte_consumos_' . $frecuencia . '_' . date('Ymd_His') . '.xlsx';
        Excel::store(new ConsumoxEmpleadoExport($fakeRequest, $idPlanta), $filename, 'local');

        return $filename;
    }
}