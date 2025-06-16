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
use App\Exports\PermisosExport;
use App\Exports\AreasExport;
use Illuminate\Support\Facades\Log;


class ClientController extends Controller
{
    // Dashboard del Cliente (Estado de Vendings, Indicadores, Cosnumos Recientes, Graficas)
    public function Home(){
        
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $areas = DB::table('Cat_Area')->select('Id_Area', 'Txt_Nombre')->get();
        return view('cliente.empleados', compact('areas'));
    }

    public function checkPermission(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $idPlanta = $_SESSION['usuario']->Id_Planta;
        try {
            $existingPermission = DB::table('Ctrl_Permisos_x_Area')
            ->where('Id_Area', $request->input('Id_Area'))
            ->where('Id_Articulo', $request->input('Id_Articulo'))
            ->where('Id_Planta', $idPlanta)
            ->exists();

        return response()->json(['exists' => $existingPermission]);
        } catch (\Exception $e) {
            Log::error('Error en la función store: ' . $e->getMessage());
            return response()->json(['error' => 'Error en el proceso.'], 500);
        }

        
    }

    public function exportPermisos(Request $request)
    {
        ini_set('max_execution_time', 300); // 5 minutos

        // Puedes personalizar el nombre del archivo que se descargará
        $fileName = 'Reporte_Permisos_' . now()->format('Ymd_His') . '.xlsx';

        // Generar y descargar el archivo Excel
        return Excel::download(new PermisosExport, $fileName);
    }
    
    public function addPermission(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $idPlanta = $_SESSION['usuario']->Id_Planta;
        try {
            DB::table('Ctrl_Permisos_x_Area')->insert([
                'Id_Area' => $request->input('Id_Area'),
                'Id_Articulo' => $request->input('Id_Articulo'),
                'Frecuencia' => $request->input('Frecuencia'),
                'Cantidad' => $request->input('Cantidad'),
                'Id_Planta' => $idPlanta,
                'Status' => 'Alta',
            ]);
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en la función store: ' . $e->getMessage());
            return response()->json(['error' => 'Error en el proceso.'], 500);
        }

        
    }

    public function PermisosArticulos(){
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $idPlanta = $_SESSION['usuario']->Id_Planta;
    
    $areas = DB::table('Cat_Area')
                ->where('Id_Planta', $idPlanta)
                ->get();

    $articulos = DB::table('Cat_Articulos')
                ->get();
                //dd($articulos);
        return view('cliente.permisos', compact('areas', 'articulos'));

    }

    public function PermisosArticulosFilter($lang,$areaId){
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $idPlanta = $_SESSION['usuario']->Id_Planta;
    $QAreaName = DB::table('Cat_Area')
    ->where('Id_Planta', $idPlanta)
    ->where('Id_Area', $areaId)
    ->first();
    $areaName= $QAreaName->Txt_Nombre;
    
    
    $areas = DB::table('Cat_Area')
                ->where('Id_Planta', $idPlanta)
                ->get();

    $articulos = DB::table('Cat_Articulos')
                ->get();
                //dd($articulos);
        return view('cliente.perarea', compact('areas', 'articulos','areaId','areaName'));

    }

    public function getPermisosArticulos(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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

    public function getPermisosPorArea($areaId)
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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
                        ->where('Ctrl_Permisos_x_Area.Id_Area', $areaId)
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
        // Obtener el Id_Area asociado al permiso
        $idArea = DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->value('Id_Area');

        // Verificar si el estado del área es "Baja"
        $areaStatus = DB::table('Cat_Area')->where('Id_Area', $idArea)->value('Txt_Estatus');
        if ($areaStatus == 'Baja') {
            return response()->json(['error' => 'No se puede cambiar el estado del permiso porque el área está dada de baja. Cambie el estado del área antes de continuar.'], 400);
        }

        // Obtener el estado actual del permiso
        $currentStatus = DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->value('Status');
        
        // Determinar el nuevo estado
        $newStatus = $currentStatus == 'Alta' ? 'Baja' : 'Alta';
        
        // Actualizar el estado del permiso
        DB::table('Ctrl_Permisos_x_Area')->where('Id_Permiso', $id)->update(['Status' => $newStatus]);

        return response()->json(['success' => 'El estado del permiso se ha actualizado con éxito.']);
    } catch (\Exception $e) {
        Log::error('Error al actualizar el estado del permiso de artículo: ' . $e->getMessage());
        return response()->json(['error' => 'Hubo un error al intentar actualizar el estado del permiso de artículo. Por favor, intente nuevamente más tarde.'], 500);
    }
}
    
public function getDataEmpleados()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $data = DB::table('Cat_Empleados')
        ->select(
            'Cat_Empleados.Id_Empleado',
            'Cat_Empleados.Nombre',
            'Cat_Empleados.APaterno',
            'Cat_Empleados.AMaterno',
            'Cat_Empleados.No_Empleado',
            'Cat_Empleados.Nip',
            'Cat_Empleados.No_Tarjeta',
            'Cat_Empleados.Id_Area',
            'Cat_Empleados.Tipo_Acceso',
            'Cat_Empleados.Fecha_alta',
            'Cat_Empleados.Fecha_Modificacion',
            'Cat_Empleados.Txt_Estatus',
            'Cat_Area.Txt_Nombre as NArea' // Se une la tabla Cat_Area
        )
        ->leftJoin('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
        ->where('Cat_Empleados.Id_Planta', $_SESSION['usuario']->Id_Planta)
        ->where('Cat_Empleados.Txt_Estatus', 'Alta')
        ->get();

    // Convertir las fechas antes de enviarlas a DataTables
    foreach ($data as $empleado) {
        $empleado->AFecha = \Carbon\Carbon::parse($empleado->Fecha_alta)->format('l, j F Y H:i:s');
        $empleado->MFecha = \Carbon\Carbon::parse($empleado->Fecha_Modificacion)->format('l, j F Y H:i:s');
    }

    return DataTables::of($data)->make(true);
}

    public function exportExcel() {
        return Excel::download(new EmpleadosExport, 'empleados.xlsx');
    }

    public function exportCSV() {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
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

    public function importCSV(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $id_planta = $_SESSION['usuario']->Id_Planta;
    $usuario = $_SESSION['usuario']->Id_Usuario;

    $status = 'success';
    $message = 'Datos importados correctamente.';
    $nuevas_areas = [];

    if ($request->hasFile('csv_file')) {
        $path = $request->file('csv_file')->getRealPath();

        $lines = file($path);
        $encodedLines = array_map(function ($line) {
            $encoding = mb_detect_encoding($line, ['ISO-8859-1', 'Windows-1252', 'UTF-8'], true);
            return mb_convert_encoding($line, 'UTF-8', $encoding ?: 'ISO-8859-1');
        }, $lines);

        $data = array_map('str_getcsv', $encodedLines);

        if (count($data) > 0) {
            $header = array_shift($data);

            // Precargar áreas con función anónima compatible
            $areas = DB::table('Cat_Area')
                ->where('Id_Planta', $id_planta)
                ->pluck('Id_Area', 'Txt_Nombre')
                ->mapWithKeys(function ($id, $nombre) {
                    return [mb_strtoupper(trim($nombre)) => $id];
                });

            foreach ($data as $row) {
                $no_empleado = !empty($row[0]) ? (string) $row[0] : null;
                $nip = !empty($row[1]) ? (string) $row[1] : '1234';
                $no_tarjeta = !empty($row[2]) ? (string) $this->sanitizeString($row[2]) : null;
                $nombre = !empty($row[3]) ? $this->sanitizeString($row[3]) : null;
                $a_paterno = !empty($row[4]) ? $this->sanitizeString($row[4]) : null;
                $a_materno = !empty($row[5]) ? $this->sanitizeString($row[5]) : '';
                $n_area = !empty($row[6]) ? $this->sanitizeString($row[6]) : null;
                $estatus = !empty($row[7]) ? $this->sanitizeString($row[7]) : 'Alta';

                if (is_null($no_empleado) || empty($nombre) || empty($a_paterno) || is_null($n_area)) {
                    $status = 'error';
                    $message = "Datos incompletos para el empleado '$no_empleado'.";
                    break;
                }

                $n_area_key = mb_strtoupper(trim($n_area));
                $id_area = $areas[$n_area_key] ?? null;

                if (!$id_area) {
                    $fecha = now();
                    try {
                        DB::table('Cat_Area')->insert([
                            'Id_Planta' => $id_planta,
                            'Txt_Nombre' => $n_area,
                            'Fecha_Alta' => $fecha,
                            'Txt_Estatus' => 'Alta',
                            'Fecha_Modificacion' => null,
                            'Fecha_Baja' => null,
                            'Id_Usuario_Alta' => $usuario,
                            'Id_Usuario_Modificacion' => null,
                            'Id_Usuario_Baja' => null
                        ]);

                        $retries = 0;
                        do {
                            usleep(200000);
                            $area = DB::table('Cat_Area')
                                ->where('Id_Planta', $id_planta)
                                ->where('Txt_Nombre', $n_area)
                                ->whereDate('Fecha_Alta', $fecha->toDateString())
                                ->first();
                            $id_area = $area ? $area->Id_Area : null;
                            $retries++;
                        } while (!$id_area && $retries < 5);

                        if ($id_area) {
                            $areas[$n_area_key] = $id_area;
                            $nuevas_areas[] = $n_area;
                        } else {
                            $status = 'error';
                            $message = "No se pudo confirmar la creación del área '$n_area'.";
                            break;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error al insertar área desde importación: ' . $e->getMessage());
                        $status = 'error';
                        $message = "Error al registrar el área '$n_area'.";
                        break;
                    }
                }

                $empleado = DB::table('Cat_Empleados')->where('No_Empleado', $no_empleado)->first();

                if ($empleado) {
                    if (
                        $empleado->Nip !== $nip ||
                        $empleado->No_Tarjeta !== $no_tarjeta ||
                        $empleado->Nombre !== $nombre ||
                        $empleado->APaterno !== $a_paterno ||
                        $empleado->AMaterno !== $a_materno ||
                        $empleado->Id_Area !== $id_area ||
                        $empleado->Txt_Estatus !== $estatus
                    ) {
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
                    }
                } else {
                    if (!empty($no_tarjeta)) {
                        $tarjeta_existente = DB::table('Cat_Empleados')->where('No_Tarjeta', $no_tarjeta)->first();
                        if ($tarjeta_existente) {
                            $status = 'error';
                            $message = "El número de tarjeta '$no_tarjeta' ya está registrado para otro empleado.";
                            break;
                        }
                    }

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

    if ($status === 'success' && !empty($nuevas_areas)) {
        $message .= ' Se registraron nuevas áreas: ' . implode(', ', $nuevas_areas) . '.';
    }

    return redirect()->back()->with(['status' => $status, 'message' => $message]);
}

    

/**
 * Función para limpiar y convertir la codificación de caracteres a UTF-8
 */
private function sanitizeString($string)
{
    // Detectar y convertir a UTF-8 si es necesario
    if (!mb_detect_encoding($string, 'UTF-8', true)) {
        $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }

    // Eliminar caracteres invisibles o especiales raros
    $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);

    return trim($string);
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
            'notarjeta' => 'nullable|string|max:255',
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

    public function getAreas(Request $request)
{
    // Verifica si el usuario es administrador a través de un parámetro en la URL
    $esAdministrador = $request->query('admin', false); // Se espera ?admin=1 en la URL para indicar que es admin

    if ($esAdministrador) {
        // Obtiene Id_Planta desde la URL (debe pasarse como ?id_planta=X)
        $id_planta = $request->query('id_planta', null);
    } else {
        // Obtiene Id_Planta desde la sesión
        $id_planta = $_SESSION['usuario']->Id_Planta ?? null;
    }

    // Construye la consulta base
    $query = DB::table('Cat_Area')
        ->select('Id_Area', 'Txt_Nombre')
        ->where('Txt_Estatus', 'Alta');

    // Aplica el filtro de Id_Planta si está presente
    if (!is_null($id_planta)) {
        $query->where('Id_Planta', $id_planta);
    }

    $areas = $query->get();

    return response()->json($areas);
}

    public function destroyEmployee($Id_Empleado)
{
    try {
        // Elimina consumos relacionados primero
        DB::table('Ctrl_Consumos')->where('Id_Empleado', $Id_Empleado)->delete();

        // Ahora elimina al empleado
        DB::table('Cat_Empleados')->where('Id_Empleado', $Id_Empleado)->delete();

        return response()->json(['message' => 'Empleado eliminado con éxito.'], 200);
    } catch (\Exception $e) {
        Log::error('Error al eliminar el empleado', [
            'Id_Empleado' => $Id_Empleado,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'No se pudo eliminar el empleado.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function storeemployee(Request $request) {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $id_planta = $_SESSION['usuario']->Id_Planta;
        $usuario = $_SESSION['usuario']->Id_Usuario;

        $validated = $request->validate([
            'no_empleado' => 'required|unique:Cat_Empleados,No_Empleado',
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

    public function Areas(){
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        return view('cliente.areas');
    }

    public function getDataAreas()
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $data=array();
        $Areas = DB::table('Cat_Area')->select('Id_Area','Id_Planta','Txt_Nombre','Txt_Estatus','Fecha_Alta','Fecha_Modificacion','Fecha_Baja')->where('Id_Planta',$_SESSION['usuario']->Id_Planta)->get();
        foreach ($Areas as $area) {
            $ModFecha = Date::parse($area->Fecha_Alta);
            $AltaFecha = Date::parse($area->Fecha_Modificacion);
            $AFecha = $AltaFecha->format('l, j F Y H:i:s');
            $MFecha = $ModFecha->format('l, j F Y H:i:s');
            $area->AFecha = $AFecha;
            $area->MFecha = $MFecha;
            array_push($data, $area);
        }
        return DataTables::of($data)->make(true);
    }
    

    public function updateNameArea(Request $request)
{
    $idArea = $request->input('id_area');
    $newName = $request->input('new_name');

    $area = DB::table('Cat_Area')->where('Id_Area', $idArea)->update(['Txt_Nombre' => $newName]);

    if ($area) {
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false]);
    }
}

public function updateStatusArea(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    // Obtén los datos del request
    $idArea = $request->input('id_area');
    $newStatus = $request->input('new_status');
    $plantaId = $_SESSION['usuario']->Id_Planta; // Obtiene el Id de Planta desde la sesión

    try {
        // Inicia la transacción
        DB::beginTransaction();

        // Actualiza el estado del área en la base de datos
        $updated = DB::table('Cat_Area')
            ->where('Id_Area', $idArea)
            ->where('Id_Planta', $plantaId)
            ->update(['Txt_Estatus' => $newStatus]);

        // Verifica si se actualizó algún registro en Cat_Area
        if ($updated) {
            // Verifica si existen registros en Ctrl_Permisos_x_Area para el Id_Area
            $hasPermissions = DB::table('Ctrl_Permisos_x_Area')
                ->where('Id_Area', $idArea)
                ->where('Id_Planta', $plantaId)
                ->exists();

            // Si hay permisos asociados, actualiza su estado
            if ($hasPermissions) {
                DB::table('Ctrl_Permisos_x_Area')
                    ->where('Id_Area', $idArea)
                    ->where('Id_Planta', $plantaId)
                    ->update(['Status' => $newStatus]);
            }

            // Confirma la transacción
            DB::commit();

            return response()->json(['success' => true, 'new_status' => $newStatus]);
        } else {
            // Si no se actualizó, revierte la transacción
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'No se encontró el área o no se actualizó.']);
        }
    } catch (\Exception $e) {
        // En caso de error, revierte la transacción y captura la excepción
        DB::rollBack();
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
}


public function addArea(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
 
    $newName = $request->input('new_name');
    $currentDate = now();
    $userId = $_SESSION['usuario']->Id_Usuario;
    $plantaId = $_SESSION['usuario']->Id_Planta;


        // Verificar si el área ya existe
        $existingArea = DB::table('Cat_Area')
        ->where('Id_Planta', $plantaId)
        ->where('Txt_Nombre', $newName)
        ->first();

    if ($existingArea) {
        return response()->json(['success' => false, 'message' => 'El área ya existe.']);
    }

    try {
        // Insertamos sin obtener el ID
        DB::table('Cat_Area')->insert([
            'Id_Planta' => $plantaId,
            'Txt_Nombre' => $newName,
            'Fecha_Alta' => $currentDate,
            'Txt_Estatus' => 'Alta',
            'Fecha_Modificacion' => null,
            'Fecha_Baja' => null,
            'Id_Usuario_Alta' => $userId,
            'Id_Usuario_Modificacion' => null,
            'Id_Usuario_Baja' => null
        ]);
    } catch (\Exception $e) {
        Log::error('Error al insertar área: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error al agregar el área.']);
    }

    // 🔁 Esperar hasta que esté disponible
    $idArea = null;
    $maxRetries = 5;
    $retries = 0;

    while ($retries < $maxRetries) {
        $area = DB::table('Cat_Area')
            ->where('Txt_Nombre', $newName)
            ->where('Id_Planta', $plantaId)
            ->where('Fecha_Alta', $currentDate)
            ->first();

        if ($area) {
            $idArea = $area->Id_Area;
            break;
        }

        usleep(200000); // 200ms
        $retries++;
    }

    if (!$idArea) {
        return response()->json(['success' => false, 'message' => 'No se pudo confirmar la creación del área.']);
    }

    // Obtener máquinas
    $maquinas = DB::table('Ctrl_Mquinas')
        ->where('Id_Planta', $plantaId)
        ->pluck('Id_Maquina');

    if ($maquinas->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No hay máquinas registradas en la planta.']);
    }

    // Obtener artículos
    $articulos = DB::table('Configuracion_Maquina')
        ->whereIn('Id_Maquina', $maquinas)
        ->whereNotNull('Id_Articulo')
        ->distinct()
        ->pluck('Id_Articulo');

    if ($articulos->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No hay artículos en las máquinas vending de esta planta.']);
    }

    // Crear permisos
    $permisos = [];
    foreach ($articulos as $idArticulo) {
        $permisos[] = [
            'Id_Area' => $idArea,
            'Id_Articulo' => $idArticulo,
            'Frecuencia' => 0,
            'Cantidad' => 0,
            'Id_Planta' => $plantaId,
            'Status' => 'Alta',
        ];
    }

    try {
        DB::table('Ctrl_Permisos_x_Area')->insert($permisos);
    } catch (\Exception $e) {
        Log::error('Error al insertar permisos: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'No se pudo crear los permisos del área.']);
    }

    return response()->json(['success' => true, 'message' => 'Área y permisos creados correctamente.']);
}

public function deleteArea(Request $request)
{
    $idArea = $request->input('id_area');

    // Comprobar si existen registros en Ctrl_Permisos_x_Area
    $permisosCount = DB::table('Ctrl_Permisos_x_Area')->where('Id_Area', $idArea)->count();
    
    // Comprobar si existen registros en Cat_Empleados
    $empleadosCount = DB::table('Cat_Empleados')->where('Id_Area', $idArea)->count();

    // Mensaje de alerta según los registros encontrados
    if ($permisosCount > 0 && $empleadosCount > 0) {
        return response()->json(['success' => false, 'message' => 'Reasigne los empleados y permisos a otra área.']);
    } elseif ($permisosCount > 0) {
        return response()->json(['success' => false, 'message' => 'Reasigne los permisos a otra área.']);
    } elseif ($empleadosCount > 0) {
        return response()->json(['success' => false, 'message' => 'Reasigne los empleados a otra área.']);
    }

    // Proceder a eliminar el área si no hay registros asociados
    $deleted = DB::table('Cat_Area')->where('Id_Area', $idArea)->delete();

    if ($deleted) {
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false, 'message' => 'Error al eliminar el área.']);
    }
}

public function exportExcelAreas() {
    return Excel::download(new AreasExport, 'areas.xlsx');
}

public function generateMissingPermissions()
{
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $plantaId = $_SESSION['usuario']->Id_Planta; // Obtiene el Id de Planta desde la sesión

    // Obtener todas las áreas de la planta
    $areas = DB::table('Cat_Area')
        ->where('Id_Planta', $plantaId)
        ->where('Txt_Estatus', 'Alta')
        ->get();

    // Obtener todos los artículos de la planta
    $articulos = DB::table('Cat_Articulos')
        ->where('Txt_Estatus', 'Alta')
        ->get();

    foreach ($areas as $area) {
        foreach ($articulos as $articulo) {
            // Verificar si el permiso ya existe para el área y el artículo
            $existingPermiso = DB::table('Ctrl_Permisos_x_Area')
                ->where('Id_Area', $area->Id_Area)
                ->where('Id_Articulo', $articulo->Id_Articulo)
                ->first();

            // Si no existe, lo insertamos con Frecuencia y Cantidad en 0
            if (!$existingPermiso) {
                DB::table('Ctrl_Permisos_x_Area')->insert([
                    'Id_Area' => $area->Id_Area,
                    'Id_Articulo' => $articulo->Id_Articulo,
                    'Frecuencia' => 0,
                    'Cantidad' => 0,
                    'Id_Planta' => $plantaId,
                    'Status' => 'Alta',
                ]);
            }
        }
    }

    return response()->json(['success' => true, 'message' => 'Permisos faltantes generados correctamente.']);
}
    
}
