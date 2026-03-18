<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConsumoxEmpleadoExport;
use App\Exports\ConsumoxAreaExport;
use App\Exports\ConsumoxVendingExport;

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
        } else {
            $areas = DB::table('Cat_Area')
                ->select('Id_Area', 'Txt_Nombre')
                ->where('Txt_Estatus', 'Alta')
                ->get();

            $empleados = DB::table('Cat_Empleados')
                ->select('Id_Empleado', 'Nombre', 'APaterno', 'AMaterno')
                ->get();

            $articulosIds = DB::table('Configuracion_Maquina')
                ->whereNotNull('Id_Articulo')
                ->distinct()
                ->pluck('Id_Articulo')
                ->toArray();

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

        // Consulta base para la DataTable
        $data = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo');

        if ($request->has('planta_id') && $request->planta_id) {
            $data->where('Cat_Empleados.Id_Planta', $request->planta_id);
        }

        $data->select(
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

    public function exportConsumoEmpleado(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $request->input('planta_id');

        return Excel::download(new ConsumoxEmpleadoExport($request, $idPlanta), 'reporte_consumos.xlsx');
    }

    public function ReporteConsumoArea(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener todas las plantas para el selector
        $plantas = DB::table('Cat_Plantas')->select('Id_Planta', 'Txt_Nombre_Planta as Nombre')->get();

        $areas = [];
        $productos = [];
        $maquinas = [];

        // Si se seleccionó una planta, cargar sus datos
        if ($request->has('planta_id') && $request->planta_id) {
            $idPlanta = $request->planta_id;

            // Obtener las áreas de la tabla Cat_Area
            $areas = DB::table('Cat_Area')
                ->select('Id_Area', 'Txt_Nombre')
                ->where('Id_Planta', $idPlanta)
                ->where('Txt_Estatus', 'Alta')
                ->get();

            // 1️⃣ Obtener las máquinas de la planta
            $maquinasList = DB::table('Ctrl_Mquinas')
                ->where('Id_Planta', $idPlanta)
                ->pluck('Id_Maquina')
                ->toArray();

            // 2️⃣ Obtener los artículos configurados en esas máquinas
            $articulosIds = DB::table('Configuracion_Maquina')
                ->whereIn('Id_Maquina', $maquinasList)
                ->whereNotNull('Id_Articulo')
                ->distinct()
                ->pluck('Id_Articulo')
                ->toArray();

            // 3️⃣ Obtener los detalles de los artículos
            $productos = DB::table('Cat_Articulos')
                ->whereIn('Id_Articulo', $articulosIds)
                ->select('Id_Articulo', 'Txt_Descripcion')
                ->get();
        } else {
            // Obtener las áreas de la tabla Cat_Area
            $areas = DB::table('Cat_Area')
                ->select('Id_Area', 'Txt_Nombre')
                ->where('Txt_Estatus', 'Alta')
                ->get();

            $articulosIds = DB::table('Configuracion_Maquina')
                ->whereNotNull('Id_Articulo')
                ->distinct()
                ->pluck('Id_Articulo')
                ->toArray();

            $productos = DB::table('Cat_Articulos')
                ->whereIn('Id_Articulo', $articulosIds)
                ->select('Id_Articulo', 'Txt_Descripcion')
                ->get();
        }

        return view('administracion.reportes.consumoxarea', compact('plantas', 'areas', 'productos'));
    }

    public function getReporteConsumoAreaData(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Consulta base para la DataTable enfocada en consumos por área
        $consumos = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->leftJoin(DB::raw('(
                select b.Id_Maquina, b.Talla, c.Codigo_Clientte as Txt_Codigo_Cliente, a.Id_Articulo, a.Id_Consumo, d.Txt_Descripcion, d.Txt_Codigo 
                from Ctrl_Consumos as a
                inner join Configuracion_Maquina as b on a.Id_Maquina = b.Id_Maquina and a.Seleccion = b.Seleccion 
                right join Codigos_Clientes as c on b.Id_Articulo = c.Id_Articulo and b.Talla = c.Talla
                inner join Cat_Articulos as d on a.Id_Articulo = d.Id_Articulo 
            ) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo');

        if ($request->has('planta_id') && $request->planta_id) {
            $consumos->where('Cat_Empleados.Id_Planta', $request->planta_id);
        }

        $consumos->select(
            'Cat_Area.Txt_Nombre as Area',
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total_Consumo'),
            DB::raw('COUNT(DISTINCT Cat_Empleados.Id_Empleado) as Numero_de_Empleados'),
            DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as Nombre_Empleado"),
            DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
            DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
            DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente) as Codigo_Cliente"),
            DB::raw('MAX(Ctrl_Consumos.Fecha_Real) as Ultimo_Consumo')
        )
            ->groupBy(
                'Cat_Area.Txt_Nombre',
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'')"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo)"),
                DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente)"),
                'Cat_Empleados.Nombre',
                'Cat_Empleados.APaterno',
                'Cat_Empleados.AMaterno'
            )
            ->orderByDesc('Ultimo_Consumo');

        // Aplicar filtros
        if ($request->filled('area')) {
            if (is_array($request->area)) {
                $consumos->whereIn('Cat_Area.Txt_Nombre', $request->area);
            } else {
                $consumos->where('Cat_Area.Txt_Nombre', '=', $request->area);
            }
        }
        if ($request->filled('product')) {
            if (is_array($request->product)) {
                $consumos->whereIn('Cat_Articulos.Txt_Descripcion', $request->product);
            } else {
                $consumos->where('Cat_Articulos.Txt_Descripcion', '=', $request->product);
            }
        }
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $consumos->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        $result = $consumos->get();

        $data = $result->groupBy(function ($item) {
            return $item->Area . '|' . $item->Producto . '|' . $item->Codigo_Urvina . '|' . $item->Codigo_Cliente;
        })->map(function ($group) {
            return [
                'Area' => $group[0]->Area,
                'Total_Consumo' => $group->sum('Total_Consumo'),
                'Numero_de_Empleados' => $group->unique('Nombre_Empleado')->count(),
                'Nombres_Empleados' => $group->pluck('Nombre_Empleado')->implode(', '),
                'Producto' => $group[0]->Producto,
                'Codigo_Urvina' => $group[0]->Codigo_Urvina,
                'Codigo_Cliente' => $group[0]->Codigo_Cliente,
                'Ultimo_Consumo' => $group->max('Ultimo_Consumo'),
            ];
        });

        $totalRecords = $data->count();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data->values()->toArray()
        ]);
    }

    public function exportConsumoArea(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $request->input('planta_id');

        return Excel::download(new ConsumoxAreaExport($request, $idPlanta), 'consumos-area.xlsx');
    }

    public function ReporteConsumoVending(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $plantas = DB::table('Cat_Plantas')->select('Id_Planta', 'Txt_Nombre_Planta as Nombre')->get();

        $areas = [];
        $productos = [];
        $maquinas = [];

        if ($request->has('planta_id') && $request->planta_id) {
            $idPlanta = $request->planta_id;

            $areas = DB::table('Cat_Area')
                ->select('Id_Area', 'Txt_Nombre')
                ->where('Id_Planta', $idPlanta)
                ->where('Txt_Estatus', 'Alta')
                ->get();

            $maquinasList = DB::table('Ctrl_Mquinas')
                ->where('Id_Planta', $idPlanta)
                ->pluck('Id_Maquina')
                ->toArray();

            $articulosIds = DB::table('Configuracion_Maquina')
                ->whereIn('Id_Maquina', $maquinasList)
                ->whereNotNull('Id_Articulo')
                ->distinct()
                ->pluck('Id_Articulo')
                ->toArray();

            $productos = DB::table('Cat_Articulos')
                ->whereIn('Id_Articulo', $articulosIds)
                ->select('Id_Articulo', 'Txt_Descripcion')
                ->get();

            $maquinas = DB::table('Ctrl_Mquinas')
                ->select('Id_Maquina', 'Txt_Nombre')
                ->where('Id_Planta', $idPlanta)
                ->get();
        } else {
            $areas = DB::table('Cat_Area')
                ->select('Id_Area', 'Txt_Nombre')
                ->where('Txt_Estatus', 'Alta')
                ->get();

            $articulosIds = DB::table('Configuracion_Maquina')
                ->whereNotNull('Id_Articulo')
                ->distinct()
                ->pluck('Id_Articulo')
                ->toArray();

            $productos = DB::table('Cat_Articulos')
                ->whereIn('Id_Articulo', $articulosIds)
                ->select('Id_Articulo', 'Txt_Descripcion')
                ->get();

            $maquinas = DB::table('Ctrl_Mquinas')
                ->select('Id_Maquina', 'Txt_Nombre')
                ->get();
        }

        return view('administracion.reportes.consumoxvending', compact('plantas', 'areas', 'productos', 'maquinas'));
    }

    public function getReporteConsumoVendingData(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $data = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->leftJoin(DB::raw('(
                select b.Id_Maquina, b.Talla, c.Codigo_Clientte as Txt_Codigo_Cliente, a.Id_Articulo, a.Id_Consumo, d.Txt_Descripcion, d.Txt_Codigo 
                from Ctrl_Consumos as a
                inner join Configuracion_Maquina as b on a.Id_Maquina = b.Id_Maquina and a.Seleccion = b.Seleccion 
                right join Codigos_Clientes as c on b.Id_Articulo = c.Id_Articulo and b.Talla = c.Talla
                inner join Cat_Articulos as d on a.Id_Articulo = d.Id_Articulo 
            ) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo');

        if ($request->has('planta_id') && $request->planta_id) {
            $data->where('Cat_Empleados.Id_Planta', $request->planta_id);
        }

        $data->groupBy(
            'Ctrl_Mquinas.Txt_Nombre',
            'Ctrl_Consumos.Id_Articulo',
            DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'')"),
            DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente)"),
            DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo)"),
            'Cat_Area.Txt_Nombre'
        )
            ->select(
                'Ctrl_Mquinas.Txt_Nombre as Maquina',
                DB::raw('COUNT(Ctrl_Consumos.Id_Articulo) as Total_Consumos'),
                DB::raw('COUNT(DISTINCT Ctrl_Consumos.Id_Empleado) as No_Empleados'),
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
                DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente) as Codigo_Cliente"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
                'Cat_Area.Txt_Nombre as Area',
                DB::raw('MAX(Ctrl_Consumos.Fecha_Real) as Ultimo_Consumo')
            );

        if ($request->filled('area')) {
            if (is_array($request->area)) {
                $data->whereIn('Cat_Area.Txt_Nombre', $request->area);
            } else {
                $data->where('Cat_Area.Txt_Nombre', '=', "$request->area");
            }
        }

        if ($request->filled('product')) {
            if (is_array($request->product)) {
                $data->where(function ($query) use ($request) {
                    $query->whereIn('Cat_Articulos.Txt_Descripcion', $request->product);
                });
            } else {
                $data->where(function ($query) use ($request) {
                    $query->where('Cat_Articulos.Txt_Descripcion', '=', $request->product);
                });
            }
        }

        if ($request->filled('vending')) {
            if (is_array($request->vending)) {
                $data->whereIn('Ctrl_Consumos.Id_Maquina', $request->vending);
            } else {
                $data->where('Ctrl_Consumos.Id_Maquina', '=', "{$request->vending}");
            }
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        $totalRecords = $data->get()->count();
        $filteredRecords = $data->get()->count();

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

    public function exportConsumoVending(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $request->input('planta_id');

        return Excel::download(new ConsumoxVendingExport($request, $idPlanta), 'vending-consumos.xlsx');
    }
    public function ReporteHistorialRelleno(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener todas las plantas para el selector
        $plantas = DB::table('Cat_Plantas')->select('Id_Planta', 'Txt_Nombre_Planta as Nombre')->get();

        return view('administracion.reportes.historialrelleno', compact('plantas'));
    }

    public function getReporteHistorialRellenoData(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $query = DB::table('Historial_Relleno')
            ->join('Configuracion_Maquina', 'Historial_Relleno.Id_Configuracion', '=', 'Configuracion_Maquina.Id_Configuracion')
            ->join('Ctrl_Mquinas', 'Historial_Relleno.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->join('Cat_Articulos', 'Historial_Relleno.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->leftJoin('Cat_Usuarios', 'Historial_Relleno.Id_Usuario', '=', 'Cat_Usuarios.Id_Usuario') // Asumiendo que Id_Usuario es FK a Cat_Usuarios
            ->select(
                'Historial_Relleno.Id_Historial',
                'Ctrl_Mquinas.Txt_Nombre as Maquina',
                'Cat_Articulos.Txt_Descripcion as Articulo',
                'Historial_Relleno.Cantidad_Anterior',
                'Historial_Relleno.Cantidad_Rellenada',
                'Historial_Relleno.Cantidad_Nueva',
                'Historial_Relleno.Fecha_Relleno',
                'Historial_Relleno.Tipo_Usuario',
                DB::raw("CONCAT(Cat_Usuarios.Txt_Nombre, ' ', Cat_Usuarios.Txt_ApellidoP) as Usuario")
            );

        if ($request->has('planta_id') && $request->planta_id) {
            $query->where('Ctrl_Mquinas.Id_Planta', $request->planta_id);
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $query->whereBetween('Historial_Relleno.Fecha_Relleno', [$startDate, $endDate]);
        }

        return DataTables::of($query)->make(true);
    }
}
