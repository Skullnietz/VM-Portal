<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function HomeRol(){
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        if(isset($_SESSION['usuario'])){
        if(($_SESSION['usuario']->Txt_Rol == "cliente")){
            return redirect('/cli/home-cli');
        }
        if($_SESSION ['usuario']->Txt_Rol == "administrador"){
            return redirect('/admin/home');
        }
        if($_SESSION['usuario']->Txt_Rol == "operador" ){
            return redirect('op/home');
        }
        }else{
            return redirect('/login');
        }
        

    }
}
