<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use DateTime;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmpleadosExport;


class ClientController extends Controller
{
    // Dashboard del Cliente (Estado de Vendings, Indicadores, Cosnumos Recientes, Graficas)
    public function Home(){
        
        session_start();
        $userId = $_SESSION['usuario']->Id_Usuario;

        $unreadNotifications = DB::table('vending_notifications')
            ->where('User_Id', $userId)
            ->whereNull('read_at')
            ->get();
        
        $Codigocliente = DB::table('Cat_Plantas')->select('Txt_Nombre_Planta','Txt_Sitio','Txt_Codigo_Cliente')->where('Id_Planta',$_SESSION['usuario']->Id_Planta)->get();
        return view('cliente.home', compact('unreadNotifications'))->with('Codigocliente',$Codigocliente);
    } 
    // Tabla de Empleados con Opciones de Creación y Modificacion
    public function Empleados(){
        session_start();
        $areas = DB::table('Cat_Area')->select('Id_Area', 'Txt_Nombre')->get();
        return view('cliente.empleados', compact('areas'));
    }

    public function PermisosArticulos(){
        session_start();
        return view('cliente.permisos');

    }
    public function getPermisosArticulos(Request $request)
    {
        session_start();
        try {
            
                if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Planta)) {
                    $idPlanta = $_SESSION['usuario']->Id_Planta;
                    $data = DB::table('Ctrl_Permisos_x_Area')
                        ->join('Cat_Area', 'Ctrl_Permisos_x_Area.Id_Area', '=', 'Cat_Area.Id_Area')
                        ->join('Cat_Articulos', 'Ctrl_Permisos_x_Area.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
                        ->select(
                            'Ctrl_Permisos_x_Area.Id_Permiso as Clave',
                            'Cat_Area.Txt_Nombre as Nombre',
                            DB::raw("CONCAT(SUBSTRING(Cat_Articulos.Txt_Descripcion, 1, 50), CASE WHEN LEN(Cat_Articulos.Txt_Descripcion) > 50 THEN '...' ELSE '' END) as Articulo"),
                            'Ctrl_Permisos_x_Area.Status as Estatus',
                            'Ctrl_Permisos_x_Area.Cantidad',
                            'Ctrl_Permisos_x_Area.Frecuencia'
                        )
                        ->where('Ctrl_Permisos_x_Area.Id_Planta', $idPlanta)
                        ->get();

                    return DataTables::of($data)->make(true);
               
            }
        } catch (\Exception $e) {
            Log::error('Error obteniendo los permisos de artículos: ' . $e->getMessage());
            return response()->json(['error' => 'Error obteniendo los permisos de artículos'], 500);
        }
    }

    public function deletePermisoArticulo(Request $request, $id)
    {
        try {
            DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->delete();
            return response()->json(['success' => 'Registro eliminado con éxito']);
        } catch (\Exception $e) {
            Log::error('Error eliminando el permiso de artículo: ' . $e->getMessage());
            return response()->json(['error' => 'Error eliminando el permiso de artículo'], 500);
        }
    }

    public function updatePermisoArticulo(Request $request, $id)
    {
        try {
            DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->update([
                $request->field => $request->value
            ]);
            return response()->json(['success' => 'Registro actualizado con éxito']);
        } catch (\Exception $e) {
            Log::error('Error actualizando el permiso de artículo: ' . $e->getMessage());
            return response()->json(['error' => 'Error actualizando el permiso de artículo'], 500);
        }
    }

    public function toggleStatusPermiso(Request $request, $id)
    {
        try {
            $currentStatus = DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->value('Status');
            $newStatus = $currentStatus == 'Alta' ? 'Baja' : 'Alta';
            DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->update(['Status' => $newStatus]);
            return response()->json(['success' => 'Estado actualizado con éxito']);
        } catch (\Exception $e) {
            Log::error('Error actualizando el estado del permiso de artículo: ' . $e->getMessage());
            return response()->json(['error' => 'Error actualizando el estado del permiso de artículo'], 500);
        }
    }
    
    public function getDataEmpleados()
    {
        session_start();
        $data=array();
        $Empleados = DB::table('Cat_Empleados')->select('Id_Empleado','Nombre','APaterno','AMaterno','No_Empleado','Nip','No_Tarjeta','Id_Area','Tipo_Acceso','Fecha_alta','Fecha_Modificacion','Txt_Estatus')->where('Id_Planta',$_SESSION['usuario']->Id_Planta)->get();
        foreach ($Empleados as $empleado) {
            $ModFecha = Date::parse($empleado->Fecha_alta);
            $AltaFecha = Date::parse($empleado->Fecha_Modificacion);
            $AFecha = $AltaFecha->format('l, j F Y H:i:s');
            $MFecha = $ModFecha->format('l, j F Y H:i:s');
            $empleado->AFecha = $AFecha;
            $empleado->MFecha = $MFecha;
            $QArea= DB::table('Cat_Area')->select('Txt_Nombre')->where('Id_Area',$empleado->Id_Area)->get();
            $empleado->NArea = $QArea[0]->Txt_Nombre;
            array_push($data, $empleado);
        }
        return DataTables::of($data)->make(true);
    }

    public function exportExcel() {
        return Excel::download(new EmpleadosExport, 'empleados.xlsx');
    }

    public function exportCSV() {
        session_start();
        $filename = "empleados_" . date('Ymd') . ".csv";
        $empleados = DB::table('Cat_Empleados')
            ->select('No_Empleado', 'Nip','No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'Id_Area', 'Txt_Estatus', 'Tipo_Acceso')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->get();
    
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
    
        $columns = array('No_Empleado', 'Nip','No_Tarjeta','Nombre', 'APaterno', 'AMaterno', 'NArea', 'Txt_Estatus');
    
        $callback = function() use($empleados, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach ($empleados as $empleado) {
                $area = DB::table('Cat_Area')->where('Id_Area', $empleado->Id_Area)->value('Txt_Nombre');
                fputcsv($file, array(
                    (string)$empleado->No_Empleado, 
                    (string)$empleado->Nip, 
                    (string)$empleado->No_Tarjeta, 
                    $empleado->Nombre, 
                    $empleado->APaterno, 
                    $empleado->AMaterno, 
                    $area,
                    $empleado->Txt_Estatus,
                ));
            }
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }

    public function importCSV(Request $request) {
        session_start();
        $id_planta = $_SESSION['usuario']->Id_Planta;
        $usuario = $_SESSION['usuario']->Id_Usuario;
    
        $status = 'success';
        $message = 'Datos importados correctamente.';
    
        if ($request->hasFile('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
    
            if (count($data) > 0) {
                $header = array_shift($data); // Obtener y eliminar el encabezado
    
                foreach ($data as $row) {
                    $no_empleado = !empty($row[0]) ? $row[0] : null;
                    $nip = !empty($row[1]) ? $row[1] : '1234';
                    $no_tarjeta = !empty($row[2]) ? $row[2] : '';
                    $nombre = !empty($row[3]) ? $row[3] : null;
                    $a_paterno = !empty($row[4]) ? $row[4] : null;
                    $a_materno = !empty($row[5]) ? $row[5] : '';
                    $n_area = !empty($row[6]) ? $row[6] : null;
                    $estatus = !empty($row[7]) ? $row[7] : 'Alta';
    
                    if (is_null($no_empleado)) {
                        $status = 'error';
                        $message = htmlspecialchars("El campo No_Empleado está vacío. No se ha importado este registro.");
                        break;
                    }
    
                    if (empty($nombre) || empty($a_paterno)) {
                        $status = 'error';
                        $message = htmlspecialchars("El campo Nombre y/o Apellido Paterno está vacío para el empleado '$no_empleado'. No se ha importado este registro.");
                        break;
                    }
    
                    if (is_null($n_area)) {
                        $status = 'error';
                        $message = htmlspecialchars("El campo de área está vacío para el empleado '$no_empleado'. No se ha importado este registro.");
                        break;
                    } else {
                        $id_area = DB::table('Cat_Area')->where('Txt_Nombre', $n_area)->value('Id_Area');
    
                        if (!$id_area) {
                            $status = 'error';
                            $message = htmlspecialchars("El área '$n_area' no se encontró en la base de datos para el empleado '$no_empleado'. No se ha importado este registro.");
                            break;
                        }
                    }
    
                    $empleado = DB::table('Cat_Empleados')->where('No_Empleado', $no_empleado)->first();
    
                    if ($empleado) {
                        // Actualizar empleado existente sin verificar el No_Tarjeta
                        DB::table('Cat_Empleados')
                            ->where('No_Empleado', $no_empleado)
                            ->update([
                                'Nip' => $nip,
                                'No_Tarjeta' => $no_tarjeta,
                                'Nombre' => $nombre,
                                'APaterno' => $a_paterno,
                                'AMaterno' => $a_materno,
                                'Id_Area' => $id_area,
                                'Fecha_Modificacion' => now(),
                                'Txt_Estatus' => $estatus,
                                'Id_Usuario_Modificacion' => $usuario,
                            ]);
                    } else {
                        // Verificar si el No_Tarjeta ya existe antes de crear un nuevo registro
                        if (!empty($no_tarjeta)) {
                            $tarjeta_existente = DB::table('Cat_Empleados')->where('No_Tarjeta', $no_tarjeta)->first();
    
                            if ($tarjeta_existente) {
                                $status = 'error';
                                $message = htmlspecialchars("El número de tarjeta '$no_tarjeta' ya está registrado para otro empleado. No se ha importado este registro.");
                                break;
                            }
                        }
    
                        // Crear nuevo empleado
                        DB::table('Cat_Empleados')->insert([
                            'No_Empleado' => $no_empleado,
                            'Nip' => $nip,
                            'No_Tarjeta' => $no_tarjeta,
                            'Nombre' => $nombre,
                            'APaterno' => $a_paterno,
                            'AMaterno' => $a_materno,
                            'Id_Area' => $id_area,
                            'Id_Planta' => $id_planta,
                            'Fecha_alta' => now(),
                            'Fecha_Modificacion' => now(),
                            'Txt_Estatus' => 'Alta',
                            'Tipo_Acceso' => 'E',
                            'Id_Usuario_Alta' => $usuario,
                            'Id_Usuario_Modificacion' => $usuario,
                            'Id_Usuario_Baja' => NULL,
                        ]);
                    }
                }
            }
        } else {
            $status = 'error';
            $message = 'No se seleccionó ningún archivo.';
        }
    
        return redirect()->back()->with(['status' => $status, 'message' => $message]);
    }
    
    public function toggleStatus($id)
    {
        $empleado = DB::table('Cat_Empleados')->where('Id_Empleado', $id)->first();
        $nuevoEstatus = $empleado->Txt_Estatus === 'Alta' ? 'Baja' : 'Alta';

        DB::table('Cat_Empleados')
            ->where('Id_Empleado', $id)
            ->update(['Txt_Estatus' => $nuevoEstatus]);

        return response()->json(['success' => true]);
    }

    public function updateemployee(Request $request, $id){
        // Valida los datos de entrada
        $validated = $request->validate([
            'id' => 'required|exists:Cat_Empleados,Id_Empleado',
            'nip' => 'required|string|max:4',
            'notarjeta' => 'string|max:255',
            'nombre' => 'required|string|max:255',
            'apaterno' => 'required|string|max:255',
            'amaterno' => 'nullable|string|max:255',
            'area' => 'required|exists:Cat_Area,Id_Area'
        ]);

        // Verificar si No_Tarjeta ya existe en otro empleado
    if (!empty($validated['notarjeta'])) {
        $tarjetaExistente = DB::table('Cat_Empleados')
            ->where('No_Tarjeta', $validated['notarjeta'])
            ->where('Id_Empleado', '!=', $id)
            ->exists();

        if ($tarjetaExistente) {
            return response()->json(['success' => false, 'message' => 'El número de tarjeta ya está registrado para otro empleado.'], 400);
        }
    }

        // Actualiza el empleado en la base de datos usando DB facade
        DB::table('Cat_Empleados')->where('Id_Empleado', $id)->update([
            'Nip' => $validated['nip'],
            'No_Tarjeta' => $validated['notarjeta'],
            'Nombre' => $validated['nombre'],
            'APaterno' => $validated['apaterno'],
            'AMaterno' => $validated['amaterno'],
            'Id_Area' => $validated['area']
        ]);

        return response()->json(['success' => true], 200);
        
    }

    public function getAreas()
    {
        // Obtiene todas las áreas de la tabla 'Cat_Area'
        $areas = DB::table('Cat_Area')->select('Id_Area', 'Txt_Nombre')->get();
        
        return response()->json($areas);
    }

    public function destroyEmployee($Id_Empleado)
    {
        try {
            // Encuentra el empleado por Id_Empleado
            $empleado = DB::table('Cat_Empleados')->where('Id_Empleado', $Id_Empleado)->first();

            if ($empleado) {
                // Elimina al empleado
                DB::table('Cat_Empleados')->where('Id_Empleado', $Id_Empleado)->delete();

                // Devuelve una respuesta exitosa
                return response()->json(['message' => 'Empleado eliminado con éxito.'], 200);
            } else {
                // Empleado no encontrado
                return response()->json(['message' => 'Empleado no encontrado.'], 404);
            }
        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir
            return response()->json(['message' => 'No se pudo eliminar el empleado.'], 500);
        }
    }

    public function storeemployee(Request $request) {
        session_start();
        $id_planta = $_SESSION['usuario']->Id_Planta;
        $usuario = $_SESSION['usuario']->Id_Usuario;

        $validated = $request->validate([
            'no_empleado' => 'required|integer|unique:Cat_Empleados,No_Empleado',
            'nip' => 'nullable|integer|max:9999',
            'no_tarjeta' => 'nullable|integer|unique:Cat_Empleados,No_Tarjeta',
            'nombre' => 'required|string|max:255',
            'apaterno' => 'required|string|max:255',
            'amaterno' => 'nullable|string|max:255',
            'area' => 'required|exists:Cat_Area,Id_Area'

        ]);
        

        // Aplicar valores por defecto si no se proporcionan
        $nip = $validated['nip'] ?? '1234';
        $no_tarjeta = $validated['no_tarjeta'] ?? '';
        $amaterno = $validated['amaterno'] ?? '';
    
        DB::table('Cat_Empleados')->insert([
            'No_Empleado' => $validated['no_empleado'],
            'Id_Planta' => $id_planta,
            'Nip' => $nip ,
            'No_Tarjeta' => $no_tarjeta,
            'Nombre' => $validated['nombre'],
            'APaterno' => $validated['apaterno'],
            'AMaterno' => $amaterno,
            'Id_Area' => $validated['area'],
            'Txt_Estatus' => 'Alta',
            'Tipo_Acceso' => 'E',
            'Id_Usuario_Alta' => $usuario,
            'Id_Usuario_Modificacion' => $usuario,
            'Id_Usuario_Baja' => NULL,
            'Fecha_alta' => now(),
            'Fecha_Modificacion' => now(),
            'Fecha_Baja' => NULL
        ]);
    
        return response()->json(['success' => true], 200);
    }

    
}
