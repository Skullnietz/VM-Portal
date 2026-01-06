<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Exports\ConsumoxEmpleadoExport;
use App\Exports\ConsumoxAreaExport;
use App\Exports\ConsumoxVendingExport;
use App\Exports\InventarioVM;
use App\Exports\ConsultaConsumosExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportesClienteController extends Controller
{


    public function indexInventarioVM()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Obtener las áreas de la tabla Cat_Area

        return view('cliente.reportes.inventariovm');

    }

    public function indexConsultaConsumos()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']->Id_Planta)) {
            abort(403, 'Sesión inválida: falta Id_Planta.');
        }

        $idPlantaSesion = (int) $_SESSION['usuario']->Id_Planta;

        $empleados = \DB::table('Cat_Empleados')
            ->select('Id_Empleado', 'No_Empleado', 'Nombre', 'APaterno', 'AMaterno')
            ->where('Id_Planta', $idPlantaSesion)
            ->orderBy('APaterno')->orderBy('Nombre')
            ->get();

        // Obtener artículos (productos) que estén configurados en máquinas de esta planta
        // (Lógica similar a indexConsumoxEmpleado para mostrar solo relevantes)
        $maquinasIds = \DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlantaSesion)
            ->pluck('Id_Maquina')
            ->toArray();

        $articulosIds = \DB::table('Configuracion_Maquina')
            ->whereIn('Id_Maquina', $maquinasIds)
            ->whereNotNull('Id_Articulo')
            ->distinct()
            ->pluck('Id_Articulo')
            ->toArray();

        $productos = \DB::table('Cat_Articulos')
            ->whereIn('Id_Articulo', $articulosIds)
            ->select('Id_Articulo', 'Txt_Descripcion')
            ->orderBy('Txt_Descripcion')
            ->get();

        return view('cliente.reportes.consultaconsumos', [
            'empleados' => $empleados,
            'productos' => $productos,
            'idPlantaSesion' => $idPlantaSesion,
        ]);
    }

    public function dataConsultaConsumos(\Illuminate\Http\Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']->Id_Planta)) {
            return response()->json(['data' => [], 'error' => 'Sesión inválida'], 403);
        }

        $validated = $request->validate([
            'NoEmpleado' => ['nullable', 'string', 'max:50'],
            'Articulo' => ['nullable', 'string', 'max:255'], // Filtro por nombre de artículo
        ]);

        $idPlanta = (int) $_SESSION['usuario']->Id_Planta;
        $noEmpleado = ($validated['NoEmpleado'] ?? '') === '' ? null : $validated['NoEmpleado'];
        $articuloFiltro = ($validated['Articulo'] ?? '') === '' ? null : $validated['Articulo'];

        $rows = \DB::select(
            'SET NOCOUNT ON;EXEC dbo.SP_Consulta_Consumos @Id_Planta = ?, @NoEmpleado = ?',
            [$idPlanta, $noEmpleado]
        );

        // Obtener mapa de Artículos (Descripción -> Código) para fallback
        // Usamos strtolower y trim para mejorar el matching
        $articulosData = \DB::table('Cat_Articulos')
            ->select('Txt_Descripcion', 'Txt_Codigo')
            ->get();

        $articulosMap = [];
        foreach ($articulosData as $art) {
            $key = trim(mb_strtolower($art->Txt_Descripcion));
            $articulosMap[$key] = $art->Txt_Codigo;
        }

        // Filtrado en PHP (ya que el SP no recibe Articulo)
        if ($articuloFiltro) {
            $rows = array_filter($rows, function ($r) use ($articuloFiltro) {
                // El SP devuelve columnas que pueden ser 'Articulo' o 'articulo'
                $art = $r->Articulo ?? $r->articulo ?? '';
                return $art === $articuloFiltro;
            });
            // Reindexar array
            $rows = array_values($rows);
        }

        $data = array_map(function ($r) use ($articulosMap) {
            $r = (array) $r;
            $nombreArticulo = $r['Articulo'] ?? $r['articulo'] ?? '';
            $lookupKey = trim(mb_strtolower($nombreArticulo));
            $codigoFallback = $articulosMap[$lookupKey] ?? '';

            return [
                'No_Empleado' => $r['No_Empleado'] ?? $r['no_empleado'] ?? '',
                'Nombre' => $r['Nombre'] ?? $r['nombre'] ?? '',
                'Articulo' => $nombreArticulo,
                'Frecuencia' => (int) ($r['Frecuencia'] ?? $r['frecuencia'] ?? 0),
                'Cantidad_Permitida' => (int) ($r['Cantidad_Permitida'] ?? $r['cantidad_permitida'] ?? 0),
                'Cantidad_Consumida' => (int) ($r['Cantidad_Consumida'] ?? $r['cantidad_consumida'] ?? 0),
                'Disponible' => (int) ($r['Disponible'] ?? $r['disponible'] ?? 0),
                'Codigo_Urvina' => $r['Codigo_Urvina'] ?? $r['codigo_urvina'] ?? $r['Txt_Codigo'] ?? $r['txt_codigo'] ?? $codigoFallback,
            ];
        }, $rows);

        return response()->json(['data' => $data]);
    }
    public function getInvStock($idMaquina)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        // Paso 1: Obtener el nombre y serie de las máquinas vending de la planta y la máquina específica
        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlanta)
            ->where('Id_Maquina', $idMaquina)
            ->pluck('Txt_Nombre', 'Id_Maquina')
            ->toArray();

        // Paso 2: Obtener los datos de configuración y stock de las máquinas vending
        $resultados = DB::table('Configuracion_Maquina')
            ->select('Id_Articulo', 'Id_Maquina', DB::raw('SUM(Cantidad_Max) as Total_Cantidad_Max'), DB::raw('SUM(Stock) as Total_Stock'))
            ->whereIn('Id_Maquina', array_keys($maquinas))
            ->groupBy('Id_Articulo', 'Id_Maquina')
            ->get();

        // Paso 3: Obtener las descripciones de los artículos
        $articulos = DB::table('Cat_Articulos')
            ->pluck('Txt_Descripcion', 'Id_Articulo')
            ->toArray();

        // Paso 4: Procesar los datos
        $data = $resultados->map(function ($item) use ($articulos, $maquinas) {
            return [
                'Nombre_Vending' => $maquinas[$item->Id_Maquina] ?? 'Desconocido',
                'Articulo' => $articulos[$item->Id_Articulo] ?? 'Desconocido',
                'Total_Cantidad_Max' => $item->Total_Cantidad_Max,
                'Total_Stock' => $item->Total_Stock,
            ];
        });

        // Si no hay registros, devuelve un JSON válido con data vacío
        if ($data->isEmpty()) {
            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // Si hay registros, devuelve los datos correctamente formateados
        return response()->json([
            'draw' => intval(request('draw')),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $data
        ]);
    }

    public function getInventarioVM()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Obtener los datos de la consulta
        $data = DB::table('Ctrl_Mquinas as cm')
            ->leftJoin('Stat_Mquinas as sm', 'cm.Id_Maquina', '=', 'sm.Id_Maquina')
            ->leftJoin('Cat_Dispositivo as cd', 'cm.Id_Dispositivo', '=', 'cd.Id_Dispositivo')
            ->select(
                'cm.Id_Maquina',
                'cm.Id_Planta',
                'cm.Id_Dispositivo',
                'cm.Txt_Nombre',
                'cm.Txt_Serie_Maquina',
                'cm.Txt_Tipo_Maquina',
                'cm.Txt_Estatus as Estatus_Maquina',
                'cm.Capacidad',
                'cm.Fecha_Alta',
                'cm.Fecha_Modificacion',
                'cm.Fecha_Baja',
                'sm.Per_Alm as Almacenamiento',
                'cd.Txt_Serie_Dispositivo',
                'cd.Txt_Estatus as Estatus_Dispositivo'
            )
            ->where('cm.Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->get();

        // Retornar los datos para DataTables
        // Si no hay registros, devuelve un JSON válido con data vacío
        if ($data->isEmpty()) {
            return response()->json([
                'draw' => intval(request('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // Si hay registros, devuelve los datos correctamente formateados
        return response()->json([
            'draw' => intval(request('draw')),
            'recordsTotal' => $data->count(),
            'recordsFiltered' => $data->count(),
            'data' => $data
        ]);
    }
    public function exportInventarioVM()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        return Excel::download(new InventarioVM($idPlanta), 'InventarioVM.xlsx');
    }

    public function indexConsumoxEmpleado()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Obtener las áreas de la tabla Cat_Area
        $areas = DB::table('Cat_Area')
            ->select('Id_Area', 'Txt_Nombre')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta
            ->where('Txt_Estatus', 'Alta') // Filtrar solo las áreas activas
            ->get();
        // Obtener los productos de la tabla Cat_Articulos
        $productos = DB::table('Cat_Articulos')
            ->select('Id_Articulo', 'Txt_Descripcion')
            ->get();
        // Obtener los empleados de la planta
        $empleados = DB::table('Cat_Empleados')
            ->select('Id_Empleado', 'Nombre', 'APaterno', 'AMaterno')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->get();



        // 1️⃣ Obtener las máquinas de la planta
        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
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

        // Pasar las áreas, productos y máquinas a la vista
        return view('cliente.reportes.consumoxempleado', compact('areas', 'productos', 'empleados'));
    }
    public function indexConsumoxArea()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Obtener las áreas de la tabla Cat_Area
        $areas = DB::table('Cat_Area')
            ->select('Id_Area', 'Txt_Nombre')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta
            ->where('Txt_Estatus', 'Alta') // Filtrar solo las áreas activas
            ->get();

        // 1️⃣ Obtener las máquinas de la planta
        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
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

        // Pasar las áreas, productos y máquinas a la vista
        return view('cliente.reportes.consumoxarea', compact('areas', 'productos'));
    }
    public function indexConsumoxVending()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Obtener las áreas de la tabla Cat_Area
        $areas = DB::table('Cat_Area')
            ->select('Id_Area', 'Txt_Nombre')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta
            ->where('Txt_Estatus', 'Alta') // Filtrar solo las áreas activas
            ->get();

        // 1️⃣ Obtener las máquinas de la planta
        $mquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->pluck('Id_Maquina')
            ->toArray();

        // 2️⃣ Obtener los artículos configurados en esas máquinas
        $articulosIds = DB::table('Configuracion_Maquina')
            ->whereIn('Id_Maquina', $mquinas)
            ->whereNotNull('Id_Articulo')
            ->distinct()
            ->pluck('Id_Articulo')
            ->toArray();

        // 3️⃣ Obtener los detalles de los artículos
        $productos = DB::table('Cat_Articulos')
            ->whereIn('Id_Articulo', $articulosIds)
            ->select('Id_Articulo', 'Txt_Descripcion')
            ->get();

        // Obtener las máquinas de la tabla Ctrl_Mquinas
        $maquinas = DB::table('Ctrl_Mquinas')
            ->select('Id_Maquina', 'Txt_Nombre')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta si es necesario
            ->get();

        // Pasar las áreas, productos y máquinas a la vista
        return view('cliente.reportes.consumoxvending', compact('areas', 'productos', 'maquinas'));
    }

    public function getConsumoxEmpleado(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        // Consulta base para la DataTable
        $data = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->leftJoin(DB::raw('(
                select b.Id_Maquina, b.Talla, c.Codigo_Clientte as Txt_Codigo_Cliente, a.Id_Articulo, a.Id_Consumo, d.Txt_Descripcion, d.Txt_Codigo 
                from Ctrl_Consumos as a
                inner join Configuracion_Maquina as b on a.Id_Maquina = b.Id_Maquina and a.Seleccion = b.Seleccion 
                right join Codigos_Clientes as c on b.Id_Articulo = c.Id_Articulo and b.Talla = c.Talla
                inner join Cat_Articulos as d on a.Id_Articulo = d.Id_Articulo 
            ) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo')
            ->where('Cat_Empleados.Id_Planta', $idPlanta)
            ->select(
                DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as Nombre"),
                'Cat_Empleados.No_Empleado as Numero_de_empleado',
                'Cat_Area.Txt_Nombre as Area',
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
                DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente) as Codigo_Cliente"),
                'Ctrl_Consumos.Fecha_Real as Fecha',
                'Ctrl_Consumos.Cantidad'
            )
            ->orderByDesc('Ctrl_Consumos.Fecha_Real'); // Ordena por la fecha del último consumo;

        // Aplicar filtros de área si están presentes
        if ($request->filled('area')) {
            // Si es un array, usar whereIn
            if (is_array($request->area)) {
                $data->whereIn('Cat_Area.Txt_Nombre', $request->area);
            } else {
                $data->where('Cat_Area.Txt_Nombre', '=', "$request->area");
            }
        }

        // Aplicar filtros de producto si están presentes
        if ($request->filled('product')) {
            // Si es un array, usar whereIn para múltiples productos
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

        if ($request->filled('employee')) {
            $selectedEmployees = $request->input('employee'); // Obtén las combinaciones seleccionadas

            // Aseguramos que haya empleados seleccionados
            if (!empty($selectedEmployees)) {
                // Usamos whereIn con una subconsulta de concatenación
                $data->whereIn(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), $selectedEmployees);
            }
        }



        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';

            $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }


        $totalRecords = $data->count(); // Total sin filtrar
        $filteredData = clone $data;
        $filteredRecords = $filteredData->count(); // Total después de aplicar filtros

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

    public function getConsumoxArea(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

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
            ) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo')
            ->where('Cat_Empleados.Id_Planta', $idPlanta)
            ->select(
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
            ->orderByDesc('Ultimo_Consumo'); // Ordena por la fecha del último consumo

        // Aplicar filtros de área si están presentes
        if ($request->filled('area')) {
            if (is_array($request->area)) {
                $consumos->whereIn('Cat_Area.Txt_Nombre', $request->area);
            } else {
                $consumos->where('Cat_Area.Txt_Nombre', '=', $request->area);
            }
        }

        // Aplicar filtros de producto si están presentes
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



        // Obtener los resultados
        $result = $consumos->get();

        // Procesar los resultados para combinar nombres de empleados
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

        $filteredData = clone $data;
        $filteredRecords = $filteredData->count(); // Total después de aplicar filtros
        $totalRecords = $data->count(); // Total sin filtrar

        // Devolver datos para DataTable
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data->values()->toArray()
        ]);





    }

    public function exportConsumoxEmpleado(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        return Excel::download(new ConsumoxEmpleadoExport($request, $idPlanta), 'reporte_consumos.xlsx');
    }
    public function exportConsumoxArea(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        return Excel::download(new ConsumoxAreaExport($request, $idPlanta), 'consumos-area.xlsx');
    }

    public function getConsumoxVending(Request $request)
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $idPlanta = $_SESSION['usuario']->Id_Planta;

            // Consulta para obtener la información agrupada por máquina (nombre), producto, área y fecha del último consumo
            $data = DB::table('Ctrl_Consumos')
                ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
                ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
                ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
                ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina') // Se une la tabla de máquinas
                ->leftJoin(DB::raw('(
                    select b.Id_Maquina, b.Talla, c.Codigo_Clientte as Txt_Codigo_Cliente, a.Id_Articulo, a.Id_Consumo, d.Txt_Descripcion, d.Txt_Codigo 
                    from Ctrl_Consumos as a
                    inner join Configuracion_Maquina as b on a.Id_Maquina = b.Id_Maquina and a.Seleccion = b.Seleccion 
                    right join Codigos_Clientes as c on b.Id_Articulo = c.Id_Articulo and b.Talla = c.Talla
                    inner join Cat_Articulos as d on a.Id_Articulo = d.Id_Articulo 
                ) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo')
                ->where('Cat_Empleados.Id_Planta', $idPlanta)
                ->groupBy(
                    'Ctrl_Mquinas.Txt_Nombre', // Se agrupa por el nombre de la máquina
                    'Ctrl_Consumos.Id_Articulo',
                    DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'')"),
                    DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente)"),
                    DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo)"),
                    'Cat_Area.Txt_Nombre'
                )
                ->select(
                    'Ctrl_Mquinas.Txt_Nombre as Maquina', // Se selecciona el nombre de la máquina
                    DB::raw('COUNT(Ctrl_Consumos.Id_Articulo) as Total_Consumos'), // Total de consumos del producto en la vending
                    DB::raw('COUNT(DISTINCT Ctrl_Consumos.Id_Empleado) as No_Empleados'), // Número de empleados distintos consumiendo el producto
                    DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
                    DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente) as Codigo_Cliente"),
                    DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
                    'Cat_Area.Txt_Nombre as Area', // Nombre del área
                    DB::raw('MAX(Ctrl_Consumos.Fecha_Real) as Ultimo_Consumo') // Fecha del último consumo
                );



            // Aplicar filtros de área si están presentes
            if ($request->filled('area')) {
                // Si es un array, usar whereIn
                if (is_array($request->area)) {
                    $data->whereIn('Cat_Area.Txt_Nombre', $request->area);
                } else {
                    $data->where('Cat_Area.Txt_Nombre', '=', "$request->area");
                }
            }

            // Aplicar filtros de producto si están presentes
            if ($request->filled('product')) {
                // Si es un array, usar whereIn para múltiples productos
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

            // Aplicar filtros de vending si están presentes
            if ($request->filled('vending')) {
                // Si es un array, usar whereIn para múltiples máquinas vending
                if (is_array($request->vending)) {
                    $data->whereIn('Ctrl_Consumos.Id_Maquina', $request->vending);
                } else {
                    $data->where('Ctrl_Consumos.Id_Maquina', '=', "{$request->vending}");
                }
            }

            // Filtros de fecha
            if ($request->filled('startDate') && $request->filled('endDate')) {
                $startDate = $request->startDate . ' 00:00:00';
                $endDate = $request->endDate . ' 23:59:59';

                $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
            }



            // Definición de columnas para el ordenamiento
            $columns = [
                0 => 'Ctrl_Mquinas.Txt_Nombre', // Maquina
                1 => DB::raw('COUNT(Ctrl_Consumos.Id_Articulo)'), // Total_Consumos
                2 => DB::raw('COUNT(DISTINCT Ctrl_Consumos.Id_Empleado)'), // No_Empleados
                3 => 'Cat_Area.Txt_Nombre', // Area
                4 => DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo)"), // Imagen (Codigo Urvina)
                5 => DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'')"), // Producto
                6 => DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo)"), // Codigo_Urvina
                7 => DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente)"), // Codigo_Cliente
                8 => DB::raw('MAX(Ctrl_Consumos.Fecha_Real)'), // Ultimo_Consumo
            ];

            if ($request->has('order') && isset($columns[$request->input('order.0.column')])) {
                $column = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');
                $data->orderBy($column, $dir);
            } else {
                $data->orderBy('Ctrl_Mquinas.Txt_Nombre', 'asc');
            }

            // --- REFACTOR: Fetch All logic (Matching Area Logic) ---
            // Execute Query to get ALL records
            $results = $data->get();

            $totalRecords = $results->count();
            $filteredRecords = $totalRecords; // Assuming filters applied in SQL

            // Pagination in PHP (Slice)
            // This bypasses SQL OFFSET/FETCH issues while still respecting DataTables "page" size
            $pagedData = $results->slice($request->start, $request->length)->values();

            // Formatting and Analysis (UTF-8 Fix)
            $finalData = $pagedData->map(function ($row) {
                // Force UTF-8 on string fields
                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $row->$key = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }
                return $row;
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $finalData
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getConsumoxVending: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return response()->json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()], 500);
        }
    }
    public function exportConsumoxVending(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        return Excel::download(new ConsumoxVendingExport($request, $idPlanta), 'vending-consumos.xlsx');
    }



    public function verConfiguracion()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['usuario']->Id_Usuario;

        $config = DB::table('Configuracion_Reportes')
            ->where('Id_Usuario', $idUsuario)
            ->first();

        return view('cliente.reportes.configurar', compact('config'));
    }

    public function guardarConfiguracion(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['usuario']->Id_Usuario;
        $recibir = $request->has('notificaciones') ? 1 : 0;
        $frecuencia = $request->input('frecuencia');
        $email = $request->input('email');

        DB::table('Configuracion_Reportes')->updateOrInsert(
            ['Id_Usuario' => $idUsuario],
            [
                'Frecuencia' => $frecuencia,
                'Email' => $email,
                'Recibir_Notificaciones' => $recibir,
                'updated_at' => now()
            ]
        );

        return redirect()->back()->with('success', 'Configuración guardada exitosamente.');
    }

    public function exportConsultaConsumos(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario']->Id_Planta)) {
            abort(403, 'Sesión inválida: falta Id_Planta.');
        }

        $idPlanta = (int) $_SESSION['usuario']->Id_Planta;
        return Excel::download(new ConsultaConsumosExport($request, $idPlanta), 'ConsultaConsumos.xlsx');
    }

}
