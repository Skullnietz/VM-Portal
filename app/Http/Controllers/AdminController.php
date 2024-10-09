<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    public function Home(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.home');
    }
    public function AdminView(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.usuarios.administradores');
    }
    public function Usuarios(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.usuarios.usuarios');
    }

    public function destroyAdmin($id)
    {
        try {
            // Busca el administrador por su ID
            $admin = DB::table('Cat_Usuarios_Administradores')->where('Id_Usuario_Admon', $id)->first();

            if (!$admin) {
                return response()->json(['message' => 'Administrador no encontrado'], 404);
            }

            // Eliminar el registro
            DB::table('Cat_Usuarios_Administradores')->where('Id_Usuario_Admon', $id)->delete();

            return response()->json(['message' => 'Administrador eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el administrador: ' . $e->getMessage()], 500);
        }
    }
    
    public function getAdministradores()
    {
        $administradores = DB::table('Cat_Usuarios_Administradores as adm')
        ->select(
            DB::raw("CONCAT(adm.Txt_Nombre, ' ', adm.Txt_ApellidoP, ' ', adm.Txt_ApellidoM) as NombreCompleto"),
            'adm.Id_Usuario_Admon as id',
            'adm.Nick as NombreUsuario',
            'adm.Txt_Estatus',
            'adm.Fecha_Alta',
            'adm.Fecha_Modificacion',
            'adm.Fecha_Baja',
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = adm.Id_Usuario_Admon_Alta) as UsuarioAlta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = adm.Id_Usuario_Admon_Modificacion) as UsuarioModificacion"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = adm.Id_Usuario_Admon_Baja) as UsuarioBaja")
        )
        ->get();

        return DataTables::of($administradores)->make(true);
    }

    public function getUsuarios()
    {
        $administradores = DB::table('Cat_Usuarios as usr')
        ->select(
            DB::raw("CONCAT(usr.Txt_Nombre, ' ', usr.Txt_ApellidoP, ' ', usr.Txt_ApellidoM) as NombreCompleto"),
            'usr.Id_Usuario as id',
            'usr.Id_Planta as planta', //Seleccionar la el nombre de la planta
            'usr.Nick_Usuario as NombreUsuario',
            'usr.Txt_Puesto',
            'usr.Txt_Estatus',
            'usr.Fecha_Alta',
            'usr.Fecha_Modificacion',
            'usr.Fecha_Baja',
            DB::raw("(SELECT Txt_Nombre_Planta FROM Cat_Plantas WHERE Id_Planta = usr.Id_Planta) as Nombre_Planta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = usr.Id_Usuario_Admon_Alta) as UsuarioAlta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = usr.Id_Usuario_Admon_Modificacion) as UsuarioModificacion"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = usr.Id_Usuario_Admon_Baja) as UsuarioBaja")
        )
        ->get();

        return DataTables::of($administradores)->make(true);
    }

    public function updateEstatus(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
        $adminId = $request->id;
        $nuevoEstatus = $request->nuevoEstatus;
        $usuarioModificador = $_SESSION['usuario']->Id_Usuario_Admon; // Obtenemos el usuario actual desde la sesión

        // Verificamos si el administrador existe
        $admin = DB::table('Cat_Usuarios_Administradores')->where('Id_Usuario_Admon', $adminId)->first();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Administrador no encontrado'], 404);
        }

        // Actualizamos los campos dependiendo del nuevo estatus
        if ($nuevoEstatus == 'Alta') {
            DB::table('Cat_Usuarios_Administradores')
                ->where('Id_Usuario_Admon', $adminId)
                ->update([
                    'Txt_Estatus' => 'Alta',
                    'Fecha_Modificacion' => now(),
                    'Fecha_Baja' => null,
                    'Id_Usuario_Admon_Modificacion' => $usuarioModificador,
                    'Id_Usuario_Admon_Baja' => null,
                ]);
        } else if ($nuevoEstatus == 'Baja') {
            DB::table('Cat_Usuarios_Administradores')
                ->where('Id_Usuario_Admon', $adminId)
                ->update([
                    'Txt_Estatus' => 'Baja',
                    'Fecha_Baja' => now(),
                    'Id_Usuario_Admon_Baja' => $usuarioModificador,
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updateEstatusUser(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
        $adminId = $request->id;
        $nuevoEstatus = $request->nuevoEstatus;
        $usuarioModificador = $_SESSION['usuario']->Id_Usuario_Admon; // Obtenemos el usuario actual desde la sesión

        // Verificamos si el administrador existe
        $user = DB::table('Cat_Usuarios')->where('Id_Usuario', $userId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Administrador no encontrado'], 404);
        }

        // Actualizamos los campos dependiendo del nuevo estatus
        if ($nuevoEstatus == 'Alta') {
            DB::table('Cat_Usuarios')
                ->where('Id_Usuario', $userId)
                ->update([
                    'Txt_Estatus' => 'Alta',
                    'Fecha_Modificacion' => now(),
                    'Fecha_Baja' => null,
                    'Id_Usuario_Admon_Modificacion' => $usuarioModificador,
                    'Id_Usuario_Admon_Baja' => null,
                ]);
        } else if ($nuevoEstatus == 'Baja') {
            DB::table('Cat_Usuarios')
                ->where('Id_Usuario', $userId)
                ->update([
                    'Txt_Estatus' => 'Baja',
                    'Fecha_Baja' => now(),
                    'Id_Usuario_Admon_Baja' => $usuarioModificador,
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function agregarAdministrador(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
    // Validar los datos entrantes
    $request->validate([
        'nombre' => 'required|string|max:255',
        'apellidoP' => 'required|string|max:255',
        'apellidoM' => 'nullable|string|max:255',
        'nick' => 'required|string|max:255|unique:Cat_Usuarios_Administradores',
        'password' => 'required|string|min:6', // Puedes ajustar la longitud mínima según tus requisitos
    ]);

    // Insertar el nuevo administrador en la base de datos
    DB::table('Cat_Usuarios_Administradores')->insert([
        'Txt_Nombre' => $request->nombre,
        'Txt_ApellidoP' => $request->apellidoP,
        'Txt_ApellidoM' => $request->apellidoM,
        'Nick' => $request->nick,
        'Contrasenia' => $request->password, // Asegúrate de hash la contraseña
        'Txt_Estatus' => "Alta",
        'Fecha_Alta' => now(),
        'Id_Usuario_Admon_Alta' => $_SESSION['usuario']->Id_Usuario_Admon, // Cambia esto según tu lógica
    ]);

    return response()->json(['message' => 'Administrador agregado con éxito']);
} catch (\Exception $e) {
    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
}
}
}
