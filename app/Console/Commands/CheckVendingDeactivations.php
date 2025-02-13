<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckVendingDeactivations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vending:register-notifications';
    protected $description = 'Registra notificaciones para máquinas vending desactivadas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtener todas las plantas registradas
        $plantas = DB::table('Cat_Usuarios')->whereNotNull('Id_Planta')->distinct()->pluck('Id_Planta');

        foreach ($plantas as $plantaId) {
            // Obtener la fecha y hora actuales con Carbon en la zona horaria especificada
            $currentDateTime = DB::selectOne("SELECT CONVERT(DATETIME, GETDATE()) as currentDateTime")->currentDateTime;

            // Obtener máquinas vending desactivadas en los últimos 5 minutos
            $desactivadas = DB::table('Stat_Mquinas')
                ->join('Ctrl_Mquinas', 'Stat_Mquinas.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
                ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
                ->whereRaw('DATEDIFF(MINUTE, Stat_Mquinas.Fecha_Reg, GETDATE()) > 5')
                ->where('Ctrl_Mquinas.Txt_Estatus', 'Alta')
                ->get();

            foreach ($desactivadas as $maquina) {
                // Eliminar notificaciones anteriores para la misma máquina en los últimos 5 minutos
                DB::table('vending_notifications')
                    ->where('Id_Maquina', $maquina->Id_Maquina)
                    ->where('Id_Planta', $plantaId)
                    ->where('description', 'No hay comunicacion con el Dispositivo')
                    ->delete();

                // Registrar la nueva notificación
                DB::table('vending_notifications')->insert([
                    'Id_Planta' => $maquina->Id_Planta,
                    'Id_Maquina' => $maquina->Id_Maquina,
                    'Txt_Nombre' => $maquina->Txt_Nombre,
                    'Txt_Estatus' => $maquina->Txt_Estatus,
                    'description'=> 'No hay comunicacion con el Dispositivo',
                    'Fecha' => $currentDateTime,
                    'Fecha_Reg' => $maquina->Fecha_Reg,
                    'read_at' => null,
                ]);
            }
        }

        $this->info('Notificaciones registradas correctamente por planta.');
    }
}


