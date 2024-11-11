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
    if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Usuario)) {
        $userId = $_SESSION['usuario']->Id_Usuario;
        $notifications = DB::table('vending_notifications')
        ->where('User_Id', $userId)
        ->whereNull('read_at')
        ->orderBy('Fecha', 'desc')
        ->get();

    return response()->json($notifications);
        } else {
            // Devolver un mensaje de "Sesión de administrador" si no hay un usuario específico
            return response()->json(['message' => 'Sesión de administrador.'], 200);
        }
     }

     public function listNotifications()
     {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Usuario)) {
        $userId = $_SESSION['usuario']->Id_Usuario;
        $unreadNotifications = DB::table('vending_notifications')
        ->where('User_Id', $userId)
        ->whereNull('read_at')
        ->orderBy('Fecha', 'desc')
        ->get();

        return view('cliente.notificaciones', compact('unreadNotifications'));
        } else {
            // Devolver un mensaje de "Sesión de administrador" si no hay un usuario específico
            return response()->json(['message' => 'Sesión de administrador.'], 200);
        }
     }

    

    public function markAsRead($id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Usuario)) {
        $userId = $_SESSION['usuario']->Id_Usuario;

        DB::table('vending_notifications')
            ->where('id', $id)
            ->where('User_Id', $userId)
            ->update(['read_at' => now()]);

        return redirect()->back();
    } else {
        // Devolver un mensaje de "Sesión de administrador" si no hay un usuario específico
        return response()->json(['message' => 'Sesión de administrador.'], 200);
    }
    }
}
