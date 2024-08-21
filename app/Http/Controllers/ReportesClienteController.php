<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Exports\ConsumoxEmpleadoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportesClienteController extends Controller
{
    public function indexConsumoxEmpleado()
    {
        session_start();
        return view('cliente.reportes.consumoxempleado');
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

    // Devolver datos para DataTable
    return DataTables::of($data)->make(true);
}

public function exportConsumoxEmpleado(Request $request)
{
    session_start();
    $idPlanta = $_SESSION['usuario']->Id_Planta;

    return Excel::download(new ConsumoxEmpleadoExport($request, $idPlanta), 'consumos.xlsx');
}

}
