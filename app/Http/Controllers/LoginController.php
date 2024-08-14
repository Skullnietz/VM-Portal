<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function Login(Request $request){
        if(isset($_SESSION['usuario'])){
            return redirect()->route('home', app()->getLocale());
        }else{
            $consulta = DB::table('Cat_Usuarios')->select('Id_Usuario','Txt_Nombre','Txt_ApellidoP','Txt_ApellidoM','Nick_Usuario','Contrasenia','Txt_Puesto','Txt_Estatus','Id_Planta','Fecha_Alta','Txt_Rol','Img_URL')->where('Nick_Usuario',$request->usuario)->where('Contrasenia',$request->password)->get();
            if(isset($consulta[0])){
                $user = $consulta[0];

        
                if($user->Nick_Usuario != $request->usuario){
                    $msg = "Usuario o Contraseña Incorrecta";
                    return view('login')->with('msg', $msg);
                }
                if($user->Contrasenia != $request->password){
                    $msg = "Contraseña Incorrecta";
                    return view('login')->with('msg', $msg);
                }else{
                    
                    session_start();
                    $_SESSION['usuario'] = $user;
                    if($_SESSION['usuario']->Txt_Rol == "cliente"){
        
                        return redirect()->route('dash-cli','cli')->with('usuario', $user);
                    }
                    if($_SESSION['usuario']->Txt_Rol == "administrador"){
        
                        return redirect()->route('dash-admin')->with('usuario', $user);
                    }
                    if($_SESSION['usuario']->Txt_Rol == "operador"){
        
                        return redirect()->route('dash-op')->with('usuario', $user);
                    }
                    return redirect()->route('salir', app()->getLocale())->with('usuario', $user);
                }
            }
            
        }
    }

    public function logout()
    {
        // Borrar solo la variable 'usuario'
        session()->forget('usuario');

        // O si necesitas verificar y borrar
        // if (session()->has('usuario')) {
        //     session()->forget('usuario');
        // }
        return redirect()->back()->with('status', 'Variable de sesión "usuario" borrada exitosamente.');
    }
}
