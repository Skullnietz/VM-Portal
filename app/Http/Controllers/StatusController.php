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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $data = [];
    
        $Stats = DB::table('Stat_Mquinas')
            ->join('Cat_Plantas', 'Stat_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select(
                'Stat_Mquinas.Id_Maquina',
                'Stat_Mquinas.Id_Planta',
                'Stat_Mquinas.Per_Alm',
                'Stat_Mquinas.Fecha_Reg',
                DB::raw("CASE WHEN DATEDIFF(MINUTE, Stat_Mquinas.Fecha_Reg, GETDATE()) <= 30 THEN 'On' ELSE 'Off' END as dispo")
            )
            ->where('Stat_Mquinas.Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->get();
    
        foreach ($Stats as $Stat) {
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
    
            $data[] = $Stat;
        }
    
        return response()->json($data);
    }
    

    public function GetAllStatus() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        $data = [];
    
        // Obtenemos las máquinas y calculamos su estado directamente en SQL
        $Stats = DB::table('Stat_Mquinas')
            ->join('Cat_Plantas', 'Stat_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select(
                'Stat_Mquinas.Id_Maquina',
                'Stat_Mquinas.Id_Planta',
                'Stat_Mquinas.Per_Alm',
                'Stat_Mquinas.Fecha_Reg',
                DB::raw("CASE WHEN DATEDIFF(MINUTE, Stat_Mquinas.Fecha_Reg, GETDATE()) <= 30 THEN 'On' ELSE 'Off' END as dispo")
            )
            ->where('Cat_Plantas.Txt_Estatus', 'Alta')
            ->get();
    
        foreach ($Stats as $Stat) {
            // Obtener nombre de la planta
            $planta = DB::table('Cat_Plantas')
                ->select('Txt_Nombre_Planta')
                ->where('Id_Planta', $Stat->Id_Planta)
                ->first();
            $Stat->Nplanta = $planta ? $planta->Txt_Nombre_Planta : 'N/A';
    
            // Obtener nombre de la máquina
            $maquina = DB::table('Ctrl_Mquinas')
                ->select('Txt_Nombre')
                ->where('Id_Maquina', $Stat->Id_Maquina)
                ->first();
            $Stat->NameVM = $maquina ? $maquina->Txt_Nombre : 'N/A';
    
            $data[] = $Stat;
        }
    
        return response()->json($data);
    }

    public function ConsumosGet()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Obtener las máquinas de la planta del usuario
    $maquinas = DB::table('Ctrl_Mquinas')
        ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
        ->pluck('Id_Maquina');

    // Obtener los últimos 20 consumos de esas máquinas
    $consumos = DB::table('Ctrl_Consumos')
        ->whereIn('Ctrl_Consumos.Id_Maquina', $maquinas)
        ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->select(
            DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as NombreEmpleado"),
            'Cat_Articulos.Txt_Descripcion as NArticulo',
            'Ctrl_Mquinas.Txt_Nombre as NombreMaquina',
            'Ctrl_Consumos.Fecha_Real'
        )
        ->orderBy('Ctrl_Consumos.Fecha_Consumo', 'desc')
        ->take(20)
        ->get();

    // Agregar columna FechaHumana a cada resultado
    foreach ($consumos as $consumo) {
        $consumo->FechaHumana = Carbon::parse($consumo->Fecha_Real)->diffForHumans();
    }

    return $consumos;
}

    public function ConsumosGetAdmin() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        // Obtener los últimos 5 consumos globales (todas las plantas)
        $consumos = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select(
                DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as NombreEmpleado"),
                'Cat_Articulos.Txt_Descripcion as NArticulo',
                'Ctrl_Mquinas.Txt_Nombre as NombreMaquina',
                'Cat_Plantas.Txt_Nombre_Planta as NombrePlanta',
                'Ctrl_Consumos.Fecha_Consumo'
            )
            ->orderBy('Ctrl_Consumos.Fecha_Consumo', 'desc')
            ->take(20)
            ->get();
    
        return $consumos;
    }
    
    public function getConsumoGraph()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $plantaId = $_SESSION['usuario']->Id_Planta;
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Artículos más consumidos
    $articulos = DB::table('Ctrl_Consumos')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->select(
            DB::raw('Cat_Articulos.Id_Articulo as id'),
            DB::raw('Cat_Articulos.Txt_Codigo as nombre'),
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as total_cantidad')
        )
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->groupBy('Cat_Articulos.Id_Articulo', 'Cat_Articulos.Txt_Codigo')
        ->orderBy('total_cantidad', 'DESC')
        ->take(5)
        ->get();

    // Consumo por máquina
    $porMaquina = DB::table('Ctrl_Consumos')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->select(
            DB::raw('Ctrl_Mquinas.Txt_Nombre as maquina'),
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as total')
        )
        ->groupBy('Ctrl_Mquinas.Txt_Nombre')
        ->orderBy('total', 'DESC')
        ->get();

    // Consumo por área
    $porArea = DB::table('Ctrl_Consumos')
        ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->select(
            DB::raw('Cat_Empleados.Id_Area'),
            DB::raw('(SELECT Txt_Nombre FROM Cat_Area WHERE Cat_Area.Id_Area = Cat_Empleados.Id_Area) as area'),
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as total')
        )
        ->groupBy('Cat_Empleados.Id_Area')
        ->orderBy('total', 'DESC')
        ->get();

    // Artículos con menor consumo
    $menorConsumo = DB::table('Ctrl_Consumos')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->select(
            'Cat_Articulos.Txt_Codigo as nombre',
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as total_cantidad')
        )
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->groupBy('Cat_Articulos.Txt_Codigo')
        ->orderBy('total_cantidad', 'ASC')
        ->take(5)
        ->get();

    return response()->json([
        'articulos' => $articulos,
        'por_maquina' => $porMaquina,
        'por_area' => $porArea,
        'menor_consumo' => $menorConsumo
    ]);
}


public function getIndexDash(){
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $plantaId = $_SESSION['usuario']->Id_Planta;
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Producto más consumido (solo de la planta)
    $productoMasConsumido = DB::table('Ctrl_Consumos')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->select('Cat_Articulos.Txt_Codigo')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
        ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
        ->groupBy('Ctrl_Consumos.Id_Articulo', 'Cat_Articulos.Txt_Codigo')
        ->orderByRaw('COUNT(Ctrl_Consumos.Id_Articulo) DESC')
        ->limit(1)
        ->pluck('Cat_Articulos.Txt_Codigo')
        ->first();

    // Área de alto consumo (ya está bien)
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

    // Vendings activas (ya está bien)
    $vendingsActivas = DB::table('Stat_Mquinas')
        ->join('Ctrl_Mquinas', 'Stat_Mquinas.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
        ->whereRaw('DATEDIFF(MINUTE, Stat_Mquinas.Fecha_Reg, GETDATE()) <= 30')
        ->count();

    // Artículos consumidos (filtro por máquina -> planta)
    $articulosConsumidos = DB::table('Ctrl_Consumos')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->where('Ctrl_Mquinas.Id_Planta', $plantaId)
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

public function getAdminDash() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $currentMonth = date('m');
    $currentYear = date('Y');

    // Producto más consumido
    $productoMasConsumido = DB::table('Ctrl_Consumos')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->select('Cat_Articulos.Txt_Codigo')
        ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
        ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
        ->groupBy('Ctrl_Consumos.Id_Articulo', 'Cat_Articulos.Txt_Codigo')
        ->orderByRaw('COUNT(Ctrl_Consumos.Id_Articulo) DESC')
        ->limit(1)
        ->pluck('Cat_Articulos.Txt_Codigo')
        ->first();

    // Planta con mayor consumo
    $plantaAltoConsumo = DB::table('Ctrl_Consumos')
        ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
        ->join('Cat_Plantas', 'Cat_Empleados.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
        ->select('Cat_Plantas.Txt_Nombre_Planta')
        ->whereRaw('MONTH(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentMonth])
        ->whereRaw('YEAR(Ctrl_Consumos.Fecha_Consumo) = ?', [$currentYear])
        ->groupBy('Cat_Empleados.Id_Planta', 'Cat_Plantas.Txt_Nombre_Planta')
        ->orderByRaw('COUNT(*) DESC')
        ->limit(1)
        ->pluck('Cat_Plantas.Txt_Nombre_Planta')
        ->first();

    // Vendings activas (últimos 30 minutos)
    $vendingsActivas = DB::table('Stat_Mquinas')
        ->whereRaw('DATEDIFF(MINUTE, Fecha_Reg, GETDATE()) <= 30')
        ->count();

    // Total de vendings en plantas con estatus Alta
    $totalVendings = DB::table('Ctrl_Mquinas')
        ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
        ->where('Cat_Plantas.Txt_Estatus', 'Alta')
        ->count();

    // Artículos consumidos
    $articulosConsumidos = DB::table('Ctrl_Consumos')
        ->whereRaw('MONTH(Fecha_Consumo) = ?', [$currentMonth])
        ->whereRaw('YEAR(Fecha_Consumo) = ?', [$currentYear])
        ->sum('Cantidad');

    return response()->json([
        'producto_mas_consumido' => $productoMasConsumido,
        'planta_alto_consumo' => $plantaAltoConsumo,
        'vendings_activas' => "{$vendingsActivas}/{$totalVendings}",
        'articulos_consumidos' => $articulosConsumidos
    ]);
}

public function getAdminDashboardStats()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $currentMonth = date('m');
    $currentYear = date('Y');

    // 1. Consumo total por planta
    $consumoPorPlanta = DB::table('Ctrl_Consumos')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
        ->where('Cat_Plantas.Txt_Estatus', 'Alta')
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->select('Cat_Plantas.Txt_Nombre_Planta as planta', DB::raw('SUM(Ctrl_Consumos.Cantidad) as total_consumo'))
        ->groupBy('Cat_Plantas.Txt_Nombre_Planta')
        ->orderByDesc('total_consumo')
        ->get();

    // 2. Top 5 productos más consumidos globalmente
    $topProductos = DB::table('Ctrl_Consumos')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
        ->where('Cat_Plantas.Txt_Estatus', 'Alta')
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->select('Cat_Articulos.Txt_Codigo as producto', DB::raw('SUM(Ctrl_Consumos.Cantidad) as total'))
        ->groupBy('Cat_Articulos.Txt_Codigo')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    // 3. Consumo por planta y producto
    $consumoPorPlantaYProducto = DB::table('Ctrl_Consumos')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
        ->where('Cat_Plantas.Txt_Estatus', 'Alta')
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->select(
            'Cat_Plantas.Txt_Nombre_Planta as planta',
            'Cat_Articulos.Txt_Codigo as producto',
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as total')
        )
        ->groupBy('Cat_Plantas.Txt_Nombre_Planta', 'Cat_Articulos.Txt_Codigo')
        ->get();

    // 4. Evolución diaria del consumo en el mes actual
    $consumoPorDia = DB::table('Ctrl_Consumos')
        ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
        ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
        ->where('Cat_Plantas.Txt_Estatus', 'Alta')
        ->whereMonth('Ctrl_Consumos.Fecha_Consumo', $currentMonth)
        ->whereYear('Ctrl_Consumos.Fecha_Consumo', $currentYear)
        ->select(DB::raw('CONVERT(date, Ctrl_Consumos.Fecha_Consumo) as dia'), DB::raw('SUM(Ctrl_Consumos.Cantidad) as total'))
        ->groupBy(DB::raw('CONVERT(date, Ctrl_Consumos.Fecha_Consumo)'))
        ->orderBy('dia')
        ->get();

    return response()->json([
        'porPlanta' => $consumoPorPlanta,
        'topProductos' => $topProductos,
        'porPlantaYProducto' => $consumoPorPlantaYProducto,
        'porDia' => $consumoPorDia,
    ]);
}


}
