<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteEmpleadosMail;
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
        $diaSemana = $hoy->dayOfWeekIso; // 1 = lunes
        $diaMes = $hoy->day;

        $usuarios = DB::table('Configuracion_Reportes')->get();

        foreach ($usuarios as $config) {
            $debeEnviar = false;

            if ($config->Frecuencia === 'diario') {
                $debeEnviar = true;
            } elseif ($config->Frecuencia === 'semanal') {
                // PRODUCCION
            // } elseif ($config->Frecuencia === 'semanal' && $diaSemana === 1) {
                $debeEnviar = true;
            } elseif ($config->Frecuencia === 'mensual') {
                // PRODUCCION
            // } elseif ($config->Frecuencia === 'mensual' && $diaMes === 1) {
                $debeEnviar = true;
            }

            if ($debeEnviar) {
                $this->info("Frecuencia: {$config->Frecuencia} | Día semana: $diaSemana | Día mes: $diaMes");

                // Ya no usamos Cat_Usuarios para obtener el correo
                if ($config->Email) {
                    $usuario = DB::table('Cat_Usuarios')->where('Id_Usuario', $config->Id_Usuario)->first();
                    $idPlanta = $usuario ? $usuario->Id_Planta : null;
                
                    if (!$idPlanta) {
                        $this->warn("No se encontró planta para el usuario {$config->Id_Usuario}");
                        continue;
                    }
                
                    // 🔧 Generar el archivo antes de usarlo
                    $filename = $this->generarExcel($idPlanta, $config->Frecuencia);
                
                    // ✅ Ahora sí enviar el correo
                    Mail::to($config->Email)->send(new ReporteEmpleadosMail("Reporte automático de consumos por empleado.", $filename));
                } else {
                    $this->warn("El usuario {$config->Id_Usuario} no tiene correo configurado.");
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
            // Hoy
            $start = $end = Carbon::now();
            break;
        case 'semanal':
            // Semana actual (lunes a hoy)
            $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
            break;
        case 'mensual':
            // Mes actual (1° al día actual)
            $start = Carbon::now()->startOfMonth();
            break;
    }

    $fakeRequest = new Request([
        'area' => [],
        'product' => [],
        'employee' => [],
        'dateRange' => $start->format('Y-m-d') . ' - ' . $end->format('Y-m-d'),
    ]);

    $filename = 'reporte_consumos_' . date('Ymd_His') . '.xlsx';
    Excel::store(new ConsumoxEmpleadoExport($fakeRequest, $idPlanta), $filename, 'local');

    return $filename;
}
}
