<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class NotificationController extends Controller
{
    public function showNotifications()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Planta)) {
            $plantaId = $_SESSION['usuario']->Id_Planta;

            $notifications = DB::table('vending_notifications')
                ->where('Id_Planta', $plantaId)
                ->whereNull('read_at')
                ->orderBy('Fecha', 'desc')
                ->get();

            return response()->json($notifications);
        } else {
            return response()->json(['message' => 'Sesión de administrador.'], 200);
        }
    }

    public function listNotifications()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Planta)) {
            $plantaId = $_SESSION['usuario']->Id_Planta;

            $unreadNotifications = DB::table('vending_notifications')
                ->where('Id_Planta', $plantaId)
                ->whereNull('read_at')
                ->orderBy('Fecha', 'desc')
                ->get();

            return view('cliente.notificaciones', compact('unreadNotifications'));
        } else {
            return response()->json(['message' => 'Sesión de administrador.'], 200);
        }
    }

    public function markAsRead($id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Planta)) {
            $plantaId = $_SESSION['usuario']->Id_Planta;

            DB::table('vending_notifications')
                ->where('id', $id)
                ->where('Id_Planta', $plantaId)
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Sesión de administrador.'], 403);
        }
    }

    public function renderList()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Planta)) {
            $plantaId = $_SESSION['usuario']->Id_Planta;

            $unreadNotifications = DB::table('vending_notifications')
                ->where('Id_Planta', $plantaId)
                ->whereNull('read_at')
                ->orderBy('Fecha', 'desc')
                ->get();

            return view('cliente.partials.notificaciones_list', compact('unreadNotifications'));
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function markAllAsRead()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Planta)) {
            $plantaId = $_SESSION['usuario']->Id_Planta;

            DB::table('vending_notifications')
                ->where('Id_Planta', $plantaId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
