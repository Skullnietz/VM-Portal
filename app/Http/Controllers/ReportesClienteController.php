<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Exports\ConsumoxEmpleadoExport;
use App\Exports\ConsumoxAreaExport;
use App\Exports\ConsumoxVendingExport;
use App\Exports\InventarioVM;
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
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta si es necesario
            ->get();
        // Obtener los productos de la tabla Cat_Articulos
        $productos = DB::table('Cat_Articulos')
                ->select('Id_Articulo', 'Txt_Descripcion')
                ->get();
        // Obtener los productos de la tabla Cat_Articulos
        $empleados = DB::table('Cat_Empleados')
                ->select('Id_Empleado', 'Nombre', 'APaterno', 'AMaterno')
                ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta si es necesario
                ->get();

        // Pasar las áreas, productos y máquinas a la vista
        return view('cliente.reportes.consumoxempleado', compact('areas', 'productos','empleados'));
    }
    public function indexConsumoxArea()
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
         // Obtener las áreas de la tabla Cat_Area
        $areas = DB::table('Cat_Area')
            ->select('Id_Area', 'Txt_Nombre')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta si es necesario
            ->get();

        // Obtener los productos de la tabla Cat_Articulos
        $productos = DB::table('Cat_Articulos')
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
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta) // Filtrar por planta si es necesario
            ->get();

        // Obtener los productos de la tabla Cat_Articulos
        $productos = DB::table('Cat_Articulos')
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
            $data->where(function($query) use ($request) {
                $query->whereIn('Cat_Articulos.Txt_Descripcion', $request->product);
            });
        } else {
            $data->where(function($query) use ($request) {
                $query->where('Cat_Articulos.Txt_Descripcion', '=', $products[0]);
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

    // Filtros de fecha
    if ($request->filled('startDate') && $request->filled('endDate')) {
        $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$request->startDate, $request->endDate]);
    }

    // Devolver datos para DataTable
    return DataTables::of($data)->make(true);
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
        ->where('Cat_Empleados.Id_Planta', $idPlanta)
        ->select(
            'Cat_Area.Txt_Nombre as Area',
            DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total_Consumo'),
            DB::raw('COUNT(DISTINCT Cat_Empleados.Id_Empleado) as Numero_de_Empleados'),
            DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as Nombre_Empleado"),
            'Cat_Articulos.Txt_Descripcion as Producto',
            'Cat_Articulos.Txt_Codigo as Codigo_Urvina',
            'Cat_Articulos.Txt_Codigo_Cliente as Codigo_Cliente',
            DB::raw('MAX(Ctrl_Consumos.Fecha_Real) as Ultimo_Consumo')
        )
        ->groupBy('Cat_Area.Txt_Nombre', 'Cat_Articulos.Txt_Descripcion', 'Cat_Articulos.Txt_Codigo', 'Cat_Articulos.Txt_Codigo_Cliente', 'Cat_Empleados.Nombre', 'Cat_Empleados.APaterno', 'Cat_Empleados.AMaterno');

    // Aplicar filtros de área si están presentes
    if ($request->filled('area')) {
        // Si es un array, usar whereIn
        if (is_array($request->area)) {
            $consumos->whereIn('Cat_Area.Txt_Nombre', $request->area);
        } else {
            $consumos->where('Cat_Area.Txt_Nombre', '=', "$request->area");
        }
    }

     // Aplicar filtros de producto si están presentes
     if ($request->filled('product')) {
        // Si es un array, usar whereIn para múltiples productos
        if (is_array($request->product)) {
            $consumos->where(function($query) use ($request) {
                $query->whereIn('Cat_Articulos.Txt_Descripcion', $request->product);
            });
        } else {
            $consumos->where(function($query) use ($request) {
                $query->where('Cat_Articulos.Txt_Descripcion', '=', $request->product);
            });
        }
    }

    // Aplicar filtro de rango de fechas
    if ($request->filled('startDate') && $request->filled('endDate')) {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        // Filtrar por el campo Fecha_Real
        $consumos->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
    }

    // Obtener los resultados
    $result = $consumos->get();

    // Procesar los resultados para combinar nombres de empleados
    $data = $result->groupBy(function($item) {
        return $item->Area . '|' . $item->Producto . '|' . $item->Codigo_Urvina . '|' . $item->Codigo_Cliente;
    })->map(function($group) {
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

    

    // Devolver datos para DataTable
    return response()->json([
        'draw' => $request->input('draw'),
        'recordsTotal' => $data->count(),
        'recordsFiltered' => $data->count(),
        'data' => $data->values()->toArray()
    ]);
}
public function exportConsumoxEmpleado(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $idPlanta = $_SESSION['usuario']->Id_Planta;

    return Excel::download(new ConsumoxEmpleadoExport($request, $idPlanta), 'consumos-empleado.xlsx');
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
    ->where('Cat_Empleados.Id_Planta', $idPlanta)
    ->groupBy(
        'Ctrl_Mquinas.Txt_Nombre', // Se agrupa por el nombre de la máquina
        'Ctrl_Consumos.Id_Articulo', 
        'Cat_Articulos.Txt_Descripcion', 
        'Cat_Articulos.Txt_Codigo_Cliente', 
        'Cat_Articulos.Txt_Codigo', 
        'Cat_Area.Txt_Nombre'
    )
    ->select(
        'Ctrl_Mquinas.Txt_Nombre as Maquina', // Se selecciona el nombre de la máquina
        DB::raw('COUNT(Ctrl_Consumos.Id_Articulo) as Total_Consumos'), // Total de consumos del producto en la vending
        DB::raw('COUNT(DISTINCT Ctrl_Consumos.Id_Empleado) as No_Empleados'), // Número de empleados distintos consumiendo el producto
        'Cat_Articulos.Txt_Descripcion as Producto',
        'Cat_Articulos.Txt_Codigo_Cliente as Codigo_Cliente',
        'Cat_Articulos.Txt_Codigo as Codigo_Urvina',
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
            $data->where(function($query) use ($request) {
                $query->whereIn('Cat_Articulos.Txt_Descripcion', $request->product);
            });
        } else {
            $data->where(function($query) use ($request) {
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
        $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$request->startDate, $request->endDate]);
    }

    // Devolver datos para DataTable
    return DataTables::of($data)->make(true);
}
public function exportConsumoxVending(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $idPlanta = $_SESSION['usuario']->Id_Planta;

    return Excel::download(new ConsumoxVendingExport($request, $idPlanta), 'vending-consumos.xlsx');
}

}
