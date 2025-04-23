<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function HomeRol(){
        session_start();
        
        if(isset($_SESSION['usuario'])){
            if(isset($_SESSION['usuario']->Txt_Rol)){
                if(($_SESSION['usuario']->Txt_Rol == "cliente")){
                    return redirect('/cli/home-cli');
                }
                if($_SESSION['usuario']->Txt_Rol == "operador" ){
                    return redirect('/op/op-vendings');
                }
                if($_SESSION['usuario']->Txt_Rol == "administrador"){
                    return redirect('/admin/home-cli');
                }
            }
            else{
                return redirect('/admin/home-admin');
            }
       
        }else{
            return redirect('/login');
        }
        

    }
}
