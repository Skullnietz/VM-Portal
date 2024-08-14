<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        // Obtener todos los usuarios con planta asignada
        $usuarios = DB::table('Cat_Usuarios')->whereNotNull('id_planta')->get();

        foreach ($usuarios as $usuario) {
            $plantaId = $usuario->Id_Planta;
            $userId = $usuario->Id_Usuario; // ID del usuario actual para el registro de notificaciones

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
                    ->where('User_Id', $userId)
                    ->whereRaw('DATEDIFF(MINUTE, Fecha, GETDATE()) <= 5')
                    ->delete();

                // Registrar la nueva notificación
                DB::table('vending_notifications')->insert([
                    'User_Id' => $userId,
                    'Id_Planta' => $maquina->Id_Planta,
                    'Id_Maquina' => $maquina->Id_Maquina,
                    'Txt_Nombre' => $maquina->Txt_Nombre,
                    'Txt_Estatus' => $maquina->Txt_Estatus,
                    'Fecha' => now(),
                    'Fecha_Reg' => $maquina->Fecha_Reg,
                    'read_at' => null,
                ]);
            }
        }

        $this->info('Notificaciones registradas correctamente para todos los usuarios.');
    
    }

 }

