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
use App\Mail\ReporteEmpleadosMail;
use Illuminate\Support\Facades\Storage;


class ClientController extends Controller
{
    // Dashboard del Cliente (Estado de Vendings, Indicadores, Cosnumos Recientes, Graficas)
    public function Home()
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario;

        $unreadNotifications = DB::table('vending_notifications')
            ->where('User_Id', $userId)
            ->whereNull('read_at')
            ->get();

        $Codigocliente = DB::table('Cat_Plantas')->select('Txt_Nombre_Planta', 'Txt_Sitio', 'Txt_Codigo_Cliente')->where('Id_Planta', $_SESSION['usuario']->Id_Planta)->get();
        return view('cliente.home', compact('unreadNotifications'))->with('Codigocliente', $Codigocliente);
    }
    // Tabla de Empleados con Opciones de Creación y Modificacion
    public function Empleados()
    {
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

    public function enviarCSVporCorreo(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $idPlanta = $request->query('idPlanta');
        $filename = $this->generarCSV($idPlanta);

        $mensaje = "Este es el reporte de empleados correspondiente a la planta {$idPlanta}.";

        Mail::to('destinatario@dominio.com')->send(new ReporteEmpleadosMail($mensaje, $filename));

        return response()->json(['status' => 'Correo enviado con éxito.']);
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



    public function PermisosArticulos()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        $areas = DB::table('Cat_Area')
            ->where('Id_Planta', $idPlanta)
            ->get();

        // 🔹 Obtener las máquinas de la planta
        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlanta)
            ->pluck('Id_Maquina')
            ->toArray();

        // 🔹 Obtener los artículos configurados en esas máquinas
        $articulosIds = DB::table('Configuracion_Maquina')
            ->whereIn('Id_Maquina', $maquinas)
            ->whereNotNull('Id_Articulo')
            ->distinct()
            ->pluck('Id_Articulo')
            ->toArray();

        // 🔹 Obtener los detalles de esos artículos
        $articulos = DB::table('Cat_Articulos')
            ->whereIn('Id_Articulo', $articulosIds)
            ->select('Id_Articulo', 'Txt_Descripcion')
            ->get();

        return view('cliente.permisos', compact('areas', 'articulos'));

    }

    public function PermisosArticulosFilter($lang, $areaId)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $idPlanta = $_SESSION['usuario']->Id_Planta;

        // Nombre del área
        $QAreaName = DB::table('Cat_Area')
            ->where('Id_Planta', $idPlanta)
            ->where('Id_Area', $areaId)
            ->first();
        $areaName = $QAreaName->Txt_Nombre ?? '';

        // Todas las áreas de la planta
        $areas = DB::table('Cat_Area')
            ->where('Id_Planta', $idPlanta)
            ->get();

        // 🔹 Obtener las máquinas de la planta
        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlanta)
            ->pluck('Id_Maquina')
            ->toArray();

        // 🔹 Obtener los artículos configurados en esas máquinas
        $articulosIds = DB::table('Configuracion_Maquina')
            ->whereIn('Id_Maquina', $maquinas)
            ->whereNotNull('Id_Articulo')
            ->distinct()
            ->pluck('Id_Articulo')
            ->toArray();

        // 🔹 Obtener los detalles de esos artículos
        $articulos = DB::table('Cat_Articulos')
            ->whereIn('Id_Articulo', $articulosIds)
            ->select('Id_Articulo', 'Txt_Descripcion')
            ->get();

        return view('cliente.perarea', compact('areas', 'articulos', 'areaId', 'areaName'));
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

    public function getDataEmpleados(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $estatus = $request->input('estatus');

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
                'Cat_Area.Txt_Nombre as NArea'
            )
            ->leftJoin('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->where('Cat_Empleados.Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->when($estatus !== null && $estatus !== '', function ($query) use ($estatus) {
                return $query->where('Cat_Empleados.Txt_Estatus', $estatus);
            })
            ->get();

        foreach ($data as $empleado) {
            $empleado->AFecha = \Carbon\Carbon::parse($empleado->Fecha_alta)->format('l, j F Y H:i:s');
            $empleado->MFecha = \Carbon\Carbon::parse($empleado->Fecha_Modificacion)->format('l, j F Y H:i:s');
        }

        return DataTables::of($data)->make(true);
    }
    public function exportExcel()
    {
        return Excel::download(new EmpleadosExport, 'empleados.xlsx');
    }

    public function generarCSV($idPlanta)
    {
        $filename = "empleados_" . date('Ymd_His') . ".csv";
        $path = storage_path("app/{$filename}");

        $empleados = DB::table('Cat_Empleados')
            ->select('No_Empleado', 'Nip', 'No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'Id_Area', 'Txt_Estatus', 'Tipo_Acceso')
            ->where('Id_Planta', $idPlanta)
            ->get();

        $columns = ['No_Empleado', 'Nip', 'No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'NArea', 'Txt_Estatus'];

        $file = fopen($path, 'w');
        fputcsv($file, $columns);

        foreach ($empleados as $empleado) {
            $area = DB::table('Cat_Area')->where('Id_Area', $empleado->Id_Area)->value('Txt_Nombre');
            $data = [
                (string) $empleado->No_Empleado,
                (string) $empleado->Nip,
                (string) $empleado->No_Tarjeta,
                $empleado->Nombre,
                $empleado->APaterno,
                $empleado->AMaterno,
                $area,
                $empleado->Txt_Estatus,
            ];
            fputcsv($file, $data);
        }

        fclose($file);
        return $filename;
    }

    public function exportCSV()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $filename = "empleados_" . date('Ymd') . ".csv";
        $empleados = DB::table('Cat_Empleados')
            ->select('No_Empleado', 'Nip', 'No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'Id_Area', 'Txt_Estatus', 'Tipo_Acceso')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('No_Empleado', 'Nip', 'No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'NArea', 'Txt_Estatus');

        $callback = function () use ($empleados, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($empleados as $empleado) {
                $area = DB::table('Cat_Area')->where('Id_Area', $empleado->Id_Area)->value('Txt_Nombre');
                fputcsv($file, array(
                    (string) $empleado->No_Empleado,
                    (string) $empleado->Nip,
                    (string) $empleado->No_Tarjeta,
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
        set_time_limit(0); // Permite ejecución ilimitada

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $id_planta = $_SESSION['usuario']->Id_Planta;
        $usuario = $_SESSION['usuario']->Id_Usuario;

        $status = 'success';
        $message = 'Datos importados correctamente.';

        if ($request->hasFile('csv_file')) {
            try {
                $file = $request->file('csv_file');

                // 0. Validar que la subida fue exitosa
                if (!$file->isValid()) {
                    throw new \Exception("Error en la subida del archivo: " . $file->getErrorMessage());
                }

                // Leer del archivo temporal
                $fullPath = $file->getRealPath();
                if (!$fullPath) {
                    $fullPath = $file->getPathname();
                }

                if (empty($fullPath)) {
                    throw new \Exception("No se pudo obtener la ruta del archivo temporal.");
                }

                if (($handle = fopen($fullPath, "r")) !== FALSE) {
                    $header = fgetcsv($handle, 1000, ",");

                    // 1. Pre-cargar áreas
                    $areasCache = DB::table('Cat_Area')
                        ->where('Id_Planta', $id_planta)
                        ->pluck('Id_Area', 'Txt_Nombre')
                        ->mapWithKeys(function ($item, $key) {
                            return [strtolower($key) => $item];
                        })
                        ->toArray();

                    // 2. Pre-cargar empleados
                    $empleadosExistentes = DB::table('Cat_Empleados')
                        ->where('Id_Planta', $id_planta)
                        ->pluck('Txt_Estatus', 'No_Empleado') // Changed to pluck status
                        ->toArray();

                    // 3. Pre-cargar tarjetas
                    $tarjetasExistentes = DB::table('Cat_Empleados')
                        ->whereNotNull('No_Tarjeta')
                        ->where('No_Tarjeta', '!=', '')
                        ->pluck('No_Empleado', 'No_Tarjeta')
                        ->toArray();

                    DB::beginTransaction();

                    $importErrors = [];
                    $createdAreas = [];
                    $rowsProcessed = 0;
                    $rowNumber = 1;

                    try {
                        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $rowNumber++;
                            if (array_filter($row) == [])
                                continue;

                            $no_empleado = !empty($row[0]) ? trim($row[0]) : null;
                            $nip = !empty($row[1]) ? trim($row[1]) : '';
                            $no_tarjeta = !empty($row[2]) ? trim($this->sanitizeString($row[2])) : null;
                            $nombre = !empty($row[3]) ? trim($this->sanitizeString($row[3])) : null;
                            $a_paterno = !empty($row[4]) ? trim($this->sanitizeString($row[4])) : null;
                            $a_materno = !empty($row[5]) ? trim($this->sanitizeString($row[5])) : '';
                            $n_area = !empty($row[6]) ? trim($this->sanitizeString($row[6])) : null;
                            $estatus = !empty($row[7]) ? trim($this->sanitizeString($row[7])) : '';

                            // --- VALIDACIONES ---
                            if (empty($no_empleado) || !is_numeric($no_empleado)) {
                                $importErrors[] = "Fila $rowNumber: 'No. Empleado' inválido o vacio. Debe ser numérico.";
                                continue;
                            }
                            if (empty($nombre) || preg_match('/\d/', $nombre)) {
                                $importErrors[] = "Fila $rowNumber: 'Nombre' es obligatorio y no puede contener números.";
                                continue;
                            }
                            if (empty($a_paterno) || preg_match('/\d/', $a_paterno)) {
                                $importErrors[] = "Fila $rowNumber: 'Apellido Paterno' es obligatorio y no puede contener números.";
                                continue;
                            }
                            if (!empty($a_materno) && preg_match('/\d/', $a_materno)) {
                                $importErrors[] = "Fila $rowNumber: 'Apellido Materno' no puede contener números.";
                                continue;
                            }
                            if (empty($n_area)) {
                                $importErrors[] = "Fila $rowNumber: 'Área' es obligatoria.";
                                continue;
                            }

                            // --- MANEJO AREA ---
                            $n_area_lower = strtolower($n_area);
                            if (isset($areasCache[$n_area_lower])) {
                                $id_area = $areasCache[$n_area_lower];
                            } else {
                                $id_area = DB::table('Cat_Area')->insertGetId([
                                    'Id_Planta' => $id_planta,
                                    'Txt_Nombre' => $n_area,
                                    'Txt_Estatus' => 'Alta',
                                    'Fecha_Alta' => now(),
                                    'Fecha_Modificacion' => now(),
                                    'Id_Usuario_Alta' => $usuario,
                                    'Id_Usuario_Modificacion' => $usuario,
                                ]);
                                $areasCache[$n_area_lower] = $id_area;
                                if (!in_array($n_area, $createdAreas)) {
                                    $createdAreas[] = $n_area;
                                }
                            }

                            // --- MANEJO EMPLEADO ---
                            if (isset($empleadosExistentes[$no_empleado])) {
                                // UPDATE
                                $updateData = [
                                    'Nombre' => $nombre,
                                    'APaterno' => $a_paterno,
                                    'AMaterno' => $a_materno,
                                    'Id_Area' => $id_area,
                                    'Fecha_Modificacion' => now(),
                                    'Id_Usuario_Modificacion' => $usuario,
                                ];

                                if (empty($nip)) {
                                    $updateData['Nip'] = '1234';
                                } else {
                                    $updateData['Nip'] = $nip;
                                }

                                if (!empty($no_tarjeta)) {
                                    if (isset($tarjetasExistentes[$no_tarjeta]) && $tarjetasExistentes[$no_tarjeta] != $no_empleado) {
                                        $importErrors[] = "Fila $rowNumber: Tarjeta '$no_tarjeta' en uso por otro empleado.";
                                        continue;
                                    }
                                    $updateData['No_Tarjeta'] = $no_tarjeta;
                                    $tarjetasExistentes[$no_tarjeta] = $no_empleado;
                                } else {
                                    $updateData['No_Tarjeta'] = null;
                                }

                                if (!empty($estatus) && in_array($estatus, ['Alta', 'Baja'])) {
                                    $updateData['Txt_Estatus'] = $estatus;

                                    $currentStatus = $empleadosExistentes[$no_empleado] ?? null;

                                    if ($estatus === 'Baja') {
                                        $updateData['Nip'] = '0000';
                                    } elseif ($estatus === 'Alta' && $currentStatus === 'Baja') {
                                        // Reactivation: Reset NIP
                                        $updateData['Nip'] = '1234';
                                    }
                                }

                                DB::table('Cat_Empleados')
                                    ->where('No_Empleado', $no_empleado)
                                    ->update($updateData);

                            } else {
                                // INSERT
                                if (!empty($no_tarjeta) && isset($tarjetasExistentes[$no_tarjeta])) {
                                    $importErrors[] = "Fila $rowNumber: Tarjeta '$no_tarjeta' en uso por otro empleado.";
                                    continue;
                                }

                                $finalNip = empty($nip) ? '1234' : $nip;
                                $finalEstatus = (!empty($estatus) && in_array($estatus, ['Alta', 'Baja'])) ? $estatus : 'Alta';

                                if ($finalEstatus === 'Baja') {
                                    $finalNip = '0000';
                                } elseif ($finalEstatus === 'Alta') {
                                    $finalNip = '1234'; // Force 1234 for new Alta records
                                }

                                DB::table('Cat_Empleados')->insert([
                                    'No_Empleado' => $no_empleado,
                                    'Nip' => $finalNip,
                                    'No_Tarjeta' => empty($no_tarjeta) ? null : $no_tarjeta,
                                    'Nombre' => $nombre,
                                    'APaterno' => $a_paterno,
                                    'AMaterno' => $a_materno,
                                    'Id_Area' => $id_area,
                                    'Id_Planta' => $id_planta,
                                    'Fecha_alta' => now(),
                                    'Fecha_Modificacion' => now(),
                                    'Txt_Estatus' => $finalEstatus,
                                    'Tipo_Acceso' => 'E',
                                    'Id_Usuario_Alta' => $usuario,
                                    'Id_Usuario_Modificacion' => $usuario,
                                    'Id_Usuario_Baja' => NULL,
                                ]);

                                $empleadosExistentes[$no_empleado] = $finalEstatus; // Update cache with new status
                                if (!empty($no_tarjeta)) {
                                    $tarjetasExistentes[$no_tarjeta] = $no_empleado;
                                }
                            }
                            $rowsProcessed++;
                        }

                        DB::commit();
                        fclose($handle);

                        $message = "Proceso completado. Registros procesados: $rowsProcessed.";
                        if (count($importErrors) > 0) {
                            $status = 'warning';
                            $message .= " Se encontraron " . count($importErrors) . " lineas con errores.";
                        }

                        return redirect()->back()->with([
                            'status' => $status,
                            'message' => $message,
                            'import_errors' => $importErrors,
                            'created_areas' => $createdAreas
                        ]);

                    } catch (\Exception $e) {
                        DB::rollBack();
                        if (is_resource($handle))
                            fclose($handle);
                        throw $e;
                    }

                } else {
                    throw new \Exception("No se pudo abrir el archivo CSV.");
                }

            } catch (\Exception $e) {
                $status = 'error';
                $message = 'Error al procesar el archivo: ' . $e->getMessage();
            }
        } else {
            $status = 'error';
            $message = 'No se seleccionó ningún archivo.';
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
        $nuevoNip = $nuevoEstatus === 'Baja' ? '0000' : '1234';

        DB::table('Cat_Empleados')
            ->where('Id_Empleado', $id)
            ->update([
                'Txt_Estatus' => $nuevoEstatus,
                'Nip' => $nuevoNip
            ]);

        return response()->json(['success' => true]);
    }

    public function updateemployee(Request $request, $id)
    {
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

    public function storeemployee(Request $request)
    {
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
            'Nip' => $nip,
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

    public function Areas()
    {
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
        $data = array();
        $Areas = DB::table('Cat_Area')->select('Id_Area', 'Id_Planta', 'Txt_Nombre', 'Txt_Estatus', 'Fecha_Alta', 'Fecha_Modificacion', 'Fecha_Baja')->where('Id_Planta', $_SESSION['usuario']->Id_Planta)->get();
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

    public function showEmployeeDetail($id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPlanta = $_SESSION['usuario']->Id_Planta;

        // Fetch Employee Basic Info
        $empleado = DB::table('Cat_Empleados')
            ->leftJoin('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->select(
                'Cat_Empleados.*',
                'Cat_Area.Txt_Nombre as Area',
                DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as NombreCompleto")
            )
            ->where('Cat_Empleados.Id_Empleado', $id)
            ->where('Cat_Empleados.Id_Planta', $idPlanta)
            ->first();

        if (!$empleado) {
            return redirect()->back()->with('error', 'Empleado no encontrado o no pertenece a su planta.');
        }

        return view('cliente.empleados.detalle', compact('empleado'));
    }

    public function getEmployeeConsumptionData(Request $request, $id)
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
            ->where('Cat_Empleados.Id_Empleado', $id)
            ->select(
                'Cat_Empleados.No_Empleado as Numero_de_empleado',
                'Cat_Area.Txt_Nombre as Area',
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
                DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente) as Codigo_Cliente"),
                'Ctrl_Consumos.Fecha_Real as Fecha',
                'Ctrl_Consumos.Cantidad'
            );

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        // Clone logic for charts (before pagination)
        $chartQuery = clone $data;

        // --- Chart 1: Product Consumption (Full Range) ---
        $productStats = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->leftJoin(DB::raw('(select b.Id_Maquina, b.Talla, a.Id_Articulo, a.Id_Consumo, d.Txt_Descripcion from Ctrl_Consumos as a inner join Configuracion_Maquina as b on a.Id_Maquina = b.Id_Maquina and a.Seleccion = b.Seleccion inner join Cat_Articulos as d on a.Id_Articulo = d.Id_Articulo) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo')
            ->where('Cat_Empleados.Id_Planta', $idPlanta)
            ->where('Cat_Empleados.Id_Empleado', $id);

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $productStats->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        $productData = $productStats
            ->select(
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
                DB::raw('SUM(ISNULL(Ctrl_Consumos.Cantidad, 1)) as Total')
            )
            ->groupBy(DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'')"))
            ->get();

        // --- Chart 2: Daily Trend (Full Range) ---
        $trendStats = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->where('Cat_Empleados.Id_Planta', $idPlanta)
            ->where('Cat_Empleados.Id_Empleado', $id);

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $trendStats->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        $trendData = $trendStats
            ->select(
                DB::raw("FORMAT(Ctrl_Consumos.Fecha_Real, 'yyyy-MM-dd') as Fecha"),
                DB::raw('SUM(ISNULL(Ctrl_Consumos.Cantidad, 1)) as Total')
            )
            ->groupBy(DB::raw("FORMAT(Ctrl_Consumos.Fecha_Real, 'yyyy-MM-dd')"))
            ->orderBy(DB::raw("FORMAT(Ctrl_Consumos.Fecha_Real, 'yyyy-MM-dd')"), 'ASC')
            ->get();


        return DataTables::of($data)
            ->with([
                'chartProductData' => $productData,
                'chartTrendData' => $trendData
            ])
            ->make(true);
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

    public function exportExcelAreas()
    {
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

    public function Profile()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $user = $_SESSION['usuario'];

        // Obtener nombre de la planta
        $planta = DB::table('Cat_Plantas')
            ->where('Id_Planta', $user->Id_Planta)
            ->value('Txt_Nombre_Planta');

        return view('cliente.profile', compact('user', 'planta'));
    }

    public function updatePassword(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',             // Minimum 8 characters
                'confirmed',
                'regex:/[a-zA-Z]/',  // Must contain at least one letter
                'regex:/[0-9]/',     // Must contain at least one number
            ],
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener letras y números.',
        ]);

        $userId = $_SESSION['usuario']->Id_Usuario;

        try {
            DB::table('Cat_Usuarios')
                ->where('Id_Usuario', $userId)
                ->update([
                    'Contrasenia' => $request->password, // Guardar en texto plano como solicitado
                    'Fecha_Modificacion' => now(),
                    // 'Id_Usuario_Modificacion' => $userId // No tenemos este campo en la sesión del cliente usualmente, pero si existe se debería usar
                ]);

            // Actualizar la sesión con la nueva contraseña
            $_SESSION['usuario']->Contrasenia = $request->password;

            return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la contraseña: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'puesto' => 'nullable|string|max:255',
        ]);

        $userId = $_SESSION['usuario']->Id_Usuario;

        try {
            DB::table('Cat_Usuarios')
                ->where('Id_Usuario', $userId)
                ->update([
                    'Txt_Nombre' => $request->nombre,
                    'Txt_ApellidoP' => $request->apellidos,
                    'Txt_Puesto' => $request->puesto,
                    'Fecha_Modificacion' => now(),
                ]);

            // Actualizar la sesión con los nuevos datos
            $_SESSION['usuario']->Txt_Nombre = $request->nombre;
            $_SESSION['usuario']->Txt_ApellidoP = $request->apellidos;
            $_SESSION['usuario']->Txt_Puesto = $request->puesto;

            return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }
    }
}
