<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use DateTime;

class StatusController extends Controller
{
    public function GetStatus(){
        session_start();
        $data=array();
        $Stats = DB::table('Stat_Mquinas')->select('Id_Maquina','Id_Planta','Per_Alm','Fecha_Reg')->where('Id_Planta',$_SESSION['usuario']->Id_Planta)->get();
        $currentDateTime = new DateTime(); // Obtenemos la fecha y hora actual
        foreach ($Stats as $Stat) {
            // Convertimos Fecha_Reg a un objeto DateTime
            $fechaReg = new DateTime($Stat->Fecha_Reg); 
            // Calculamos la diferencia
            $interval = $currentDateTime->diff($fechaReg); 
            // Convertimos la diferencia a minutos
            $minutesDifference =  $interval->i; 

            
    
            // Asignar "On" o "Off" basado en la diferencia de tiempo
            $Stat->dispo = ($minutesDifference < 1) ? "On" : "Off";
            
            // Obtener el nombre de la planta
            $planta = DB::table('Cat_Plantas')
                        ->select('Txt_Nombre_Planta')
                        ->where('Id_Planta', $Stat->Id_Planta)
                        ->first();
            $Stat->Nplanta = $planta ? $planta->Txt_Nombre_Planta : 'N/A';
    
            // Obtener el nombre de la máquina
            $maquina = DB::table('Ctrl_Mquinas')
                         ->select('Txt_Nombre')
                         ->where('Id_Maquina', $Stat->Id_Maquina)
                         ->first();
            $Stat->NameVM = $maquina ? $maquina->Txt_Nombre : 'N/A';
            
            array_push($data, $Stat);
        }
        return response()->json($data);

    }

    public function ConsumosGet(){
        session_start();
        // Obtener las máquinas de la planta del usuario
        $maquinas = DB::table('Ctrl_Mquinas')
        ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
        ->pluck('Id_Maquina');

        // Obtener los últimos 5 consumos de las máquinas de la planta del usuario
        $consumos = DB::table('Ctrl_Consumos')
        ->whereIn('Ctrl_Consumos.Id_Maquina', $maquinas)
        ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->select(
            DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as NombreEmpleado"),
            'Cat_Articulos.Txt_Descripcion as NArticulo',
            'Ctrl_Mquinas.Txt_Nombre as NombreMaquina',
            'Ctrl_Consumos.Fecha_Consumo'
        )
        ->orderBy('Ctrl_Consumos.Fecha_Consumo', 'desc')
        ->take(5)
        ->get();

        return $consumos;



    }
    public function getConsumoGraph()
    {
        session_start();
        $currentMonth = date('m');
        $currentYear = date('Y');
        $consumoData = DB::table('Ctrl_Consumos')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->select(DB::raw('Cat_Articulos.Id_Articulo as id, Cat_Articulos.Txt_Codigo as nombre, SUM(Ctrl_Consumos.Cantidad) as total_cantidad'))
        ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
        ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
        ->groupBy('Cat_Articulos.Id_Articulo', 'Cat_Articulos.Txt_Codigo')
        ->orderBy('total_cantidad', 'DESC')
        ->take(5)
        ->get();

        return response()->json($consumoData);
    }

    public function getIndexDash(){
        session_start();
        $plantaId = $_SESSION['usuario']->Id_Planta;
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Obtener Producto más consumido
        $productoMasConsumido = DB::table('Ctrl_Consumos')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select('Cat_Articulos.Txt_Codigo')
            ->where('Cat_Articulos.Id_Planta', $plantaId)
            ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
            ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
            ->groupBy('Ctrl_Consumos.Id_Articulo', 'Cat_Articulos.Txt_Codigo')
            ->orderByRaw('COUNT(Ctrl_Consumos.Id_Articulo) DESC')
            ->limit(1)
            ->pluck('Cat_Articulos.Txt_Codigo')
            ->first();

        // Obtener Área de alto consumo
        $areaAltoConsumo = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->select('Cat_Area.Txt_Nombre')
            ->where('Cat_Empleados.Id_Planta', $plantaId)
            ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
            ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
            ->groupBy('Cat_Empleados.Id_Area', 'Cat_Area.Txt_Nombre')
            ->orderByRaw('COUNT(Cat_Empleados.Id_Area) DESC')
            ->limit(1)
            ->pluck('Cat_Area.Txt_Nombre')
            ->first();

        // Obtener Vendings activas
        $vendingsActivas = DB::table('Stat_Mquinas')
        ->join('Ctrl_Mquinas', 'Stat_Mquinas.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->whereRaw('DATEDIFF(MINUTE, Stat_Mquinas.Fecha_Reg, GETDATE()) <= 5')
        ->count();
            

        // Obtener Artículos consumidos
        $articulosConsumidos = DB::table('Ctrl_Consumos')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->where('Cat_Articulos.Id_Planta', $plantaId)
            ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
            ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
            ->sum('Ctrl_Consumos.cantidad');

        return response()->json([
            'producto_mas_consumido' => $productoMasConsumido,
            'area_alto_consumo' => $areaAltoConsumo,
            'vendings_activas' => $vendingsActivas,
            'articulos_consumidos' => $articulosConsumidos
        ]);
    }
}
