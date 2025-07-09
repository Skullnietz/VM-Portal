<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteEmpleadosMail;
use App\Mail\FalloSincronizacionMail;
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

        // Validar sincronización por planta
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

        // Si las sincronizaciones están bien, continúa con el envío de reportes
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
                $this->info("📤 Enviando reporte a usuario ID: {$config->Id_Usuario}");

                if ($config->Email) {
                    $usuario = DB::table('Cat_Usuarios')->where('Id_Usuario', $config->Id_Usuario)->first();
                    $idPlanta = $usuario ? $usuario->Id_Planta : null;

                    if (!$idPlanta) {
                        $this->warn("⚠️ No se encontró planta para el usuario {$config->Id_Usuario}");
                        continue;
                    }

                    $filename = $this->generarExcel($idPlanta, $config->Frecuencia);

                    Mail::to($config->Email)->send(new ReporteEmpleadosMail(
                        "Reporte automático de consumos por empleado.", $filename
                    ));
                } else {
                    $this->warn("⚠️ El usuario {$config->Id_Usuario} no tiene correo configurado.");
                }
            }
        }

        return Command::SUCCESS;
    }

    private function generarExcel($idPlanta, $frecuencia)
{
    $start = Carbon::now();
    $end = Carbon::now();

    switch ($frecuencia) {
        case 'diario':
            // Día anterior completo
            $start = Carbon::yesterday()->startOfDay();
            $end = Carbon::yesterday()->endOfDay();
            break;

        case 'semanal':
            // Semana pasada (lunes a domingo)
            $start = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
            $end = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
            break;

        case 'mensual':
            // Mes anterior (1 al último día)
            $start = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $end = Carbon::now()->subMonthNoOverflow()->endOfMonth();
            break;
    }

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