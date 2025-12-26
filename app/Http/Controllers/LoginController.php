<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function Login(Request $request)
    {
        if (isset($_SESSION['usuario'])) {
            return redirect()->route('home', app()->getLocale());
        } else {
            $consulta = DB::table('Cat_Usuarios')
                ->select('Id_Usuario', 'Txt_Nombre', 'Txt_ApellidoP', 'Txt_ApellidoM', 'Nick_Usuario', 'Contrasenia', 'Txt_Puesto', 'Txt_Estatus', 'Id_Planta', 'Fecha_Alta', 'Txt_Rol', 'Img_URL')
                ->where('Nick_Usuario', $request->usuario)
                ->where('Contrasenia', $request->password)
                ->get();

            if (isset($consulta[0])) {
                $user = $consulta[0];

                if ($user->Nick_Usuario != $request->usuario) {
                    $msg = "Usuario o Contraseña Incorrecta";
                    return redirect()->back()->withErrors(['msg' => $msg]);
                }
                if ($user->Contrasenia != $request->password) {
                    $msg = "Contraseña Incorrecta";
                    return redirect()->back()->withErrors(['msg' => $msg]);
                } else {
                    session_start();
                    $_SESSION['usuario'] = $user;

                    // RECORDARME LOGIC
                    if ($request->has('remember')) {
                        $tokenData = [
                            'id' => $user->Id_Usuario,
                            'type' => 'cliente'
                        ];
                        $encryptedToken = Crypt::encrypt($tokenData);
                        Cookie::queue('vm_remember_token', $encryptedToken, 43200); // 30 days
                    }

                    if ($_SESSION['usuario']->Txt_Rol == "cliente") {
                        return redirect()->route('dash-cli', 'cli')->with('usuario', $user);
                    }
                    if ($_SESSION['usuario']->Txt_Rol == "administrador") {
                        return redirect()->route('dash-admin')->with('usuario', $user);
                    }
                    if ($_SESSION['usuario']->Txt_Rol == "operador") {
                        return redirect()->route('dash-op')->with('usuario', $user);
                    }

                    return redirect()->route('salir', app()->getLocale())->with('usuario', $user);
                }
            } else {
                $msg = "Usuario o Contraseña Incorrecta";
                return redirect()->back()->withErrors(['msg' => $msg]);
            }
        }
    }

    public function ADMINLogin(Request $request)
    {
        if (isset($_SESSION['usuario'])) {
            return redirect()->route('home', app()->getLocale());
        } else {
            $consulta = DB::table('Cat_Usuarios_Administradores')
                ->select('Id_Usuario_Admon', 'Txt_Nombre', 'Txt_ApellidoP', 'Txt_ApellidoM', 'Nick', 'Contrasenia', 'Txt_Estatus', 'Fecha_Alta')
                ->where('Nick', $request->usuario)
                ->where('Contrasenia', $request->password)
                ->get();

            if (isset($consulta[0])) {
                $user = $consulta[0];

                if ($user->Nick != $request->usuario) {
                    $msg = "Usuario o Contraseña Incorrecta";
                    return redirect()->back()->withErrors(['msg' => $msg]);
                }
                if ($user->Contrasenia != $request->password) {
                    $msg = "Contraseña Incorrecta";
                    return redirect()->back()->withErrors(['msg' => $msg]);
                } else {
                    session_start();
                    $_SESSION['usuario'] = $user;
                    $_SESSION['usuario']->Img_URL = '/Images/Usuarios/urvina.png';

                    // RECORDARME LOGIC
                    if ($request->has('remember')) {
                        $tokenData = [
                            'id' => $user->Id_Usuario_Admon,
                            'type' => 'administrador'
                        ];
                        $encryptedToken = Crypt::encrypt($tokenData);
                        Cookie::queue('vm_remember_token', $encryptedToken, 43200); // 30 days
                    }

                    return redirect()->route('dash-admin', ['language' => 'admin'])->with('usuario', $user);
                }
            } else {
                $msg = "Usuario o Contraseña Incorrecta";
                return redirect()->back()->withErrors(['msg' => $msg]);
            }
        }
    }

    public function operadorLogin(Request $request)
    {

        if (isset($_SESSION['usuario'])) {
            return redirect()->route('home', app()->getLocale());
        } else {
            $consulta = DB::table('Cat_Operadores')
                ->select('Id_Operador', 'Txt_Nombre', 'Txt_ApellidoP', 'Txt_ApellidoM', 'Nick_Usuario', 'Contrasenia', 'Txt_Puesto', 'Txt_Estatus', 'PlantasConAcceso', 'Fecha_Alta', 'Txt_Rol', 'Img_URL')
                ->where('Nick_Usuario', $request->usuario)
                ->where('Contrasenia', $request->password)
                ->get();

            if (isset($consulta[0])) {
                $user = $consulta[0];


                if ($user->Nick_Usuario != $request->usuario) {
                    $msg = "Usuario o Contraseña Incorrecta";
                    return redirect()->back()->withErrors(['msg' => $msg]);
                }
                if ($user->Contrasenia != $request->password) {
                    $msg = "Contraseña Incorrecta";
                    return redirect()->back()->withErrors(['msg' => $msg]);
                } else {
                    session_start();
                    $_SESSION['usuario'] = $user;

                    // RECORDARME LOGIC
                    if ($request->has('remember')) {
                        $tokenData = [
                            'id' => $user->Id_Operador,
                            'type' => 'operador'
                        ];
                        $encryptedToken = Crypt::encrypt($tokenData);
                        Cookie::queue('vm_remember_token', $encryptedToken, 43200); // 30 days
                    }

                    if ($_SESSION['usuario']->Txt_Rol == "operador") {
                        $_SESSION['usuario'] = $user;
                        $_SESSION['usuario']->Img_URL = '/Images/Usuarios/urvina.png';
                        return redirect()->route('op-vendings', ['language' => 'op'])->with('usuario', $user);
                    }

                    return redirect()->route('salir', app()->getLocale())->with('usuario', $user);
                }
            } else {
                $msg = "Usuario o Contraseña Incorrecta";
                return redirect()->back()->withErrors(['msg' => $msg]);
            }
        }
    }


    public function logout()
    {
        // Borrar solo la variable 'usuario'
        //session()->forget('usuario');

        // O si necesitas verificar y borrar
        // if (session()->has('usuario')) {
        //     session()->forget('usuario');
        // }

        session_start();
        unset($_SESSION['usuario']);
        session_destroy();

        // Delete Remember Me Cookie
        Cookie::queue(Cookie::forget('vm_remember_token'));

        return redirect()->route('homerol');

        //return redirect()->back()->with('status', 'Variable de sesión "usuario" borrada exitosamente.');
    }
}
