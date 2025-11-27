<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ReportesAdministradorController extends Controller
{
    public function ReporteConsumoEmpleado(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener todas las plantas para el selector
        $plantas = DB::table('Cat_Plantas')->select('Id_Planta', 'Txt_Nombre_Planta as Nombre')->get();

        $areas = [];
        $productos = [];
        $empleados = [];

        // Si se seleccionó una planta, cargar sus datos
        if ($request->has('planta_id') && $request->planta_id) {
            $idPlanta = $request->planta_id;

            // Obtener las áreas de la tabla Cat_Area
            $areas = DB::table('Cat_Area')
                ->select('Id_Area', 'Txt_Nombre')
                ->where('Id_Planta', $idPlanta)
                ->where('Txt_Estatus', 'Alta')
                ->get();

            // Obtener los empleados de la planta
            $empleados = DB::table('Cat_Empleados')
                ->select('Id_Empleado', 'Nombre', 'APaterno', 'AMaterno')
                ->where('Id_Planta', $idPlanta)
                ->get();

            // 1️⃣ Obtener las máquinas de la planta
            $maquinas = DB::table('Ctrl_Mquinas')
                ->where('Id_Planta', $idPlanta)
                ->pluck('Id_Maquina')
                ->toArray();

            // 2️⃣ Obtener los artículos configurados en esas máquinas
            $articulosIds = DB::table('Configuracion_Maquina')
                ->whereIn('Id_Maquina', $maquinas)
                ->whereNotNull('Id_Articulo')
                ->distinct()
                ->pluck('Id_Articulo')
                ->toArray();

            // 3️⃣ Obtener los detalles de los artículos
            $productos = DB::table('Cat_Articulos')
                ->whereIn('Id_Articulo', $articulosIds)
                ->select('Id_Articulo', 'Txt_Descripcion')
                ->get();
        }

        return view('administracion.reportes.consumoxempleado', compact('plantas', 'areas', 'productos', 'empleados'));
    }

    public function getReporteConsumoEmpleadoData(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$request->has('planta_id') || !$request->planta_id) {
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $idPlanta = $request->planta_id;

        // Consulta base para la DataTable
        $data = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->where('Cat_Empleados.Id_Planta', $idPlanta)
            ->select(
                DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as Nombre"),
                'Cat_Empleados.No_Empleado as Numero_de_empleado',
                'Cat_Area.Txt_Nombre as Area',
                'Cat_Articulos.Txt_Descripcion as Producto',
                'Cat_Articulos.Txt_Codigo as Codigo_Urvina',
                'Cat_Articulos.Txt_Codigo_Cliente as Codigo_Cliente',
                'Ctrl_Consumos.Fecha_Real as Fecha',
                'Ctrl_Consumos.Cantidad'
            )
            ->orderByDesc('Ctrl_Consumos.Fecha_Real');

        // Aplicar filtros de área si están presentes
        if ($request->filled('area')) {
            if (is_array($request->area)) {
                $data->whereIn('Cat_Area.Txt_Nombre', $request->area);
            } else {
                $data->where('Cat_Area.Txt_Nombre', '=', "$request->area");
            }
        }

        // Aplicar filtros de producto si están presentes
        if ($request->filled('product')) {
            if (is_array($request->product)) {
                $data->whereIn('Cat_Articulos.Txt_Descripcion', $request->product);
            } else {
                $data->where('Cat_Articulos.Txt_Descripcion', '=', "$request->product");
            }
        }

        if ($request->filled('employee')) {
            $selectedEmployees = $request->input('employee');
            if (!empty($selectedEmployees)) {
                $data->whereIn(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), $selectedEmployees);
            }
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        $totalRecords = $data->count();
        $filteredData = clone $data;
        $filteredRecords = $filteredData->count();

        $data = $data->offset($request->start)
            ->limit($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
}
