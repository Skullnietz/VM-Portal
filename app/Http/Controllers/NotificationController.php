<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class NotificationController extends Controller
{
    
     public function showNotifications()
     {
        session_start();
        $userId = $_SESSION['usuario']->Id_Usuario;
        $notifications = DB::table('vending_notifications')
        ->where('User_Id', $userId)
        ->whereNull('read_at')
        ->orderBy('Fecha', 'desc')
        ->get();

    return response()->json($notifications);
     }

     public function listNotifications()
     {
        session_start();
        $userId = $_SESSION['usuario']->Id_Usuario;
        $unreadNotifications = DB::table('vending_notifications')
        ->where('User_Id', $userId)
        ->whereNull('read_at')
        ->orderBy('Fecha', 'desc')
        ->get();

        return view('cliente.notificaciones', compact('unreadNotifications'));
     }

    

    public function markAsRead($id)
    {
        session_start();
        $userId = $_SESSION['usuario']->Id_Usuario;

        DB::table('vending_notifications')
            ->where('id', $id)
            ->where('User_Id', $userId)
            ->update(['read_at' => now()]);

        return redirect()->back();
    }
}
