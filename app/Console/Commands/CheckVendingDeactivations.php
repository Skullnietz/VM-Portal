<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\VendingDesactivadaMail;

class CheckVendingDeactivations extends Command
{
    protected $signature = 'vending:register-notifications';
    protected $description = 'Registra notificaciones para máquinas vending desactivadas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtener todas las plantas registradas
        $plantas = DB::table('Cat_Usuarios')->whereNotNull('Id_Planta')->distinct()->pluck('Id_Planta');

        foreach ($plantas as $plantaId) {
            // Fecha actual desde SQL Server
            $currentDateTime = DB::selectOne("SELECT CONVERT(DATETIME, GETDATE()) as currentDateTime")->currentDateTime;

            // Obtener máquinas desactivadas con más de 5 minutos sin comunicación
            $desactivadas = DB::table('Stat_Mquinas')
                ->join('Ctrl_Mquinas', 'Stat_Mquinas.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
                ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
                ->whereRaw('DATEDIFF(MINUTE, Stat_Mquinas.Fecha_Reg, GETDATE()) > 5')
                ->where('Ctrl_Mquinas.Txt_Estatus', 'Alta')
                ->get();

            foreach ($desactivadas as $maquina) {
                // Verifica si ya hay una notificación hoy para esta máquina
                $existeNotificacionHoy = DB::table('vending_notifications')
                    ->where('Id_Maquina', $maquina->Id_Maquina)
                    ->where('Id_Planta', $plantaId)
                    ->whereDate('Fecha', Carbon::today())
                    ->where('description', 'No hay comunicacion con el Dispositivo')
                    ->exists();

                if ($existeNotificacionHoy) {
                    $this->warn("Ya existe notificación hoy para la máquina {$maquina->Id_Maquina}.");
                    continue;
                }

                $usuarioConfig = DB::table('Configuracion_Reportes')
                    ->join('Cat_Usuarios', 'Configuracion_Reportes.Id_Usuario', '=', 'Cat_Usuarios.Id_Usuario')
                    ->where('Cat_Usuarios.Id_Planta', $plantaId)
                    ->where('Configuracion_Reportes.Recibir_Notificaciones', 1)
                    ->whereNotNull('Configuracion_Reportes.Email')
                    ->select('Configuracion_Reportes.Id_Usuario')
                    ->first();

                if (!$usuarioConfig) {
                    $this->warn("No se encontró usuario configurado para la planta {$plantaId}.");
                    continue;
                }

                // ✅ Registrar nueva notificación
                DB::table('vending_notifications')->insert([
                    'Id_Planta' => $maquina->Id_Planta,
                    'Id_Maquina' => $maquina->Id_Maquina,
                    'Txt_Nombre' => $maquina->Txt_Nombre,
                    'Txt_Estatus' => $maquina->Txt_Estatus,
                    'description'=> 'No hay comunicacion con el Dispositivo',
                    'Fecha' => $currentDateTime,
                    'Fecha_Reg' => $maquina->Fecha_Reg,
                    'read_at' => null,
                    'User_Id' => $usuarioConfig->Id_Usuario, // ✅ Aquí se soluciona el error
                ]);

                // ✅ Enviar correo solo a usuarios que lo activaron en Configuracion_Reportes
                $usuarios = DB::table('Configuracion_Reportes')
                    ->join('Cat_Usuarios', 'Configuracion_Reportes.Id_Usuario', '=', 'Cat_Usuarios.Id_Usuario')
                    ->where('Cat_Usuarios.Id_Planta', $plantaId)
                    ->where('Configuracion_Reportes.Recibir_Notificaciones', 1)
                    ->whereNotNull('Configuracion_Reportes.Email')
                    ->pluck('Configuracion_Reportes.Email');

                foreach ($usuarios as $correo) {
                    Mail::to($correo)->send(new VendingDesactivadaMail($maquina));
                }
            }
        }

        $this->info('Notificaciones registradas correctamente por planta.');
    }
}