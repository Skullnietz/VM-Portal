<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Exports\ConsumoxEmpleadoExport;
use App\Exports\ConsumoxAreaExport;
use App\Exports\ConsumoxVendingExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportesClienteController extends Controller
{
    public function indexConsumoxEmpleado()
    {
        session_start();
        return view('cliente.reportes.consumoxempleado');
    }
    public function indexConsumoxArea()
    {
        session_start();
        return view('cliente.reportes.consumoxarea');
    }
    public function indexConsumoxVending()
    {
        session_start();
        return view('cliente.reportes.consumoxvending');
    }

    public function getConsumoxEmpleado(Request $request)
{
    session_start();
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
            'Ctrl_Consumos.Fecha_Consumo as Fecha',
            'Ctrl_Consumos.Cantidad'
        );

    // Aplicar filtros si están presentes
    if ($request->filled('area')) {
        $data->where('Cat_Area.Txt_Nombre', 'like', "%{$request->area}%");
    }

    if ($request->filled('product')) {
        $data->where(function($query) use ($request) {
            $query->where('Cat_Articulos.Txt_Descripcion', 'like', "%{$request->product}%")
                ->orWhere('Cat_Articulos.Txt_Codigo', 'like', "%{$request->product}%")
                ->orWhere('Cat_Articulos.Txt_Codigo_Cliente', 'like', "%{$request->product}%");
        });
    }

    if ($request->filled('employee')) {
        $data->where(function($query) use ($request) {
            $query->where(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), 'like', "%{$request->employee}%")
                ->orWhere('Cat_Empleados.No_Empleado', 'like', "%{$request->employee}%");
        });
    }

    // Filtros de fecha
    if ($request->filled('startDate') && $request->filled('endDate')) {
        $data->whereBetween('Ctrl_Consumos.Fecha_Consumo', [$request->startDate, $request->endDate]);
    }

    // Devolver datos para DataTable
    return DataTables::of($data)->make(true);
}

public function getConsumoxArea(Request $request)
{
    session_start();
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
            DB::raw('MAX(Ctrl_Consumos.Fecha_Consumo) as Ultimo_Consumo')
        )
        ->groupBy('Cat_Area.Txt_Nombre', 'Cat_Articulos.Txt_Descripcion', 'Cat_Articulos.Txt_Codigo', 'Cat_Articulos.Txt_Codigo_Cliente', 'Cat_Empleados.Nombre', 'Cat_Empleados.APaterno', 'Cat_Empleados.AMaterno');

    // Aplicar filtros si están presentes
    if ($request->filled('area')) {
        $consumos->where('Cat_Area.Txt_Nombre', 'like', "%{$request->area}%");
    }

    if ($request->filled('product')) {
        $consumos->where(function($query) use ($request) {
            $query->where('Cat_Articulos.Txt_Descripcion', 'like', "%{$request->product}%")
                ->orWhere('Cat_Articulos.Txt_Codigo', 'like', "%{$request->product}%")
                ->orWhere('Cat_Articulos.Txt_Codigo_Cliente', 'like', "%{$request->product}%");
        });
    }

    // Aplicar filtro de rango de fechas
    if ($request->filled('startDate') && $request->filled('endDate')) {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        // Filtrar por el campo Fecha_Consumo
        $consumos->whereBetween('Ctrl_Consumos.Fecha_Consumo', [$startDate, $endDate]);
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

    // Aplicar filtros si están presentes
    if ($request->filled('area')) {
        $data = $data->filter(function($item) use ($request) {
            return stripos($item['Area'], $request->area) !== false;
        });
    }

    if ($request->filled('product')) {
        $data = $data->filter(function($item) use ($request) {
            return stripos($item['Producto'], $request->product) !== false ||
                   stripos($item['Codigo_Urvina'], $request->product) !== false ||
                   stripos($item['Codigo_Cliente'], $request->product) !== false;
        });
    }

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
    session_start();
    $idPlanta = $_SESSION['usuario']->Id_Planta;

    return Excel::download(new ConsumoxEmpleadoExport($request, $idPlanta), 'consumos-empleado.xlsx');
}
public function exportConsumoxArea(Request $request)
{
    session_start();
    $idPlanta = $_SESSION['usuario']->Id_Planta;

    return Excel::download(new ConsumoxAreaExport($request, $idPlanta), 'consumos-area.xlsx');
}

public function getConsumoxVending(Request $request)
{
    session_start();
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
        DB::raw('MAX(Ctrl_Consumos.Fecha_Consumo) as Ultimo_Consumo') // Fecha del último consumo
    );



    // Aplicar filtros si están presentes
    if ($request->filled('area')) {
        $data->where('Cat_Area.Txt_Nombre', 'like', "%{$request->area}%");
    }

    if ($request->filled('product')) {
        $data->where(function($query) use ($request) {
            $query->where('Cat_Articulos.Txt_Descripcion', 'like', "%{$request->product}%")
                ->orWhere('Cat_Articulos.Txt_Codigo', 'like', "%{$request->product}%")
                ->orWhere('Cat_Articulos.Txt_Codigo_Cliente', 'like', "%{$request->product}%");
        });
    }

    if ($request->filled('vending')) {
        $data->where('Ctrl_Consumos.Id_Maquina', '=', "{$request->vending}");
    }

    // Filtros de fecha
    if ($request->filled('startDate') && $request->filled('endDate')) {
        $data->whereBetween('Ctrl_Consumos.Fecha_Consumo', [$request->startDate, $request->endDate]);
    }

    // Devolver datos para DataTable
    return DataTables::of($data)->make(true);
}
public function exportConsumoxVending(Request $request)
{
    session_start();
    $idPlanta = $_SESSION['usuario']->Id_Planta;

    return Excel::download(new ConsumoxVendingExport($request, $idPlanta), 'vending-consumos.xlsx');
}

}
