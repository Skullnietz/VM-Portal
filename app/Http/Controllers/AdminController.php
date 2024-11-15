<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use DateTime;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmpleadosExportAdmin;
use App\Exports\PermisosExportAdmin;
use App\Exports\AreasExportAdmin;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function Home(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.home');
    }
    public function Plantas(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.plantas.plantas');
    }
    public function AdminView(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.usuarios.administradores');
    }
    public function Usuarios(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.usuarios.usuarios');
    }

    public function Articulos(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.articulos.articulos');
    }

    public function getPlantas()
    {
        $plantas = DB::table('Cat_Plantas')
                    ->select('Id_Planta', 'Txt_Nombre_Planta')
                    ->get();

        return response()->json($plantas);
    }

    public function getPlantasInfo()
    {
        $plantas = DB::table('Cat_Plantas as plta')
        ->select(
            'plta.Id_Planta as id', 'plta.Txt_Nombre_Planta','plta.Txt_Codigo_Cliente','plta.Txt_Sitio','plta.Txt_Estatus','plta.Fecha_Alta','plta.Fecha_Modificacion','Fecha_Baja','Ruta_Imagen',
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = plta.Id_Usuario_Admon_Alta) as UsuarioAlta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = plta.Id_Usuario_Admon_Modificacion) as UsuarioModificacion"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = plta.Id_Usuario_Admon_Baja) as UsuarioBaja")
        )->get();
        return DataTables::of($plantas)->make(true);
    }

    public function destroyPlanta($id)
    {
        try {
            // Verificar si la planta existe
            $planta = DB::table('Cat_Plantas')->where('Id_Planta', $id)->first();
    
            if (!$planta) {
                return response()->json(['message' => 'Planta no encontrada'], 404);
            }
    
            // Verificar si hay usuarios asignados a la planta
            $usuariosAsignados = DB::table('Cat_Usuarios')->where('Id_Planta', $id)->exists();
            if ($usuariosAsignados) {
                return response()->json(['message' => 'No se puede eliminar la planta porque tiene usuarios asignados'], 400);
            }
    
            // Verificar si hay empleados asignados a la planta
            $empleadosAsignados = DB::table('Cat_Empleados')->where('Id_Planta', $id)->exists();
            if ($empleadosAsignados) {
                return response()->json(['message' => 'No se puede eliminar la planta porque tiene empleados asignados'], 400);
            }
    
            // Verificar si hay permisos asignados a la planta
            $permisosAsignados = DB::table('Ctrl_Permisos_x_Area')->where('Id_Planta', $id)->exists();
            if ($permisosAsignados) {
                return response()->json(['message' => 'No se puede eliminar la planta porque tiene permisos asignados'], 400);
            }
    
            // Verificar si hay artículos asignados a la planta
            $articulosAsignados = DB::table('Cat_Articulos')->where('Id_Planta', $id)->exists();
            if ($articulosAsignados) {
                return response()->json(['message' => 'No se puede eliminar la planta porque tiene artículos asignados'], 400);
            }
    
            // Verificar si hay máquinas asignadas a la planta
            $maquinasAsignadas = DB::table('Ctrl_Mquinas')->where('Id_Planta', $id)->exists();
            if ($maquinasAsignadas) {
                return response()->json(['message' => 'No se puede eliminar la planta porque tiene máquinas asignadas'], 400);
            }
    
            // Si no hay relaciones, eliminar la planta
            DB::table('Cat_Plantas')->where('Id_Planta', $id)->delete();
    
            return response()->json(['message' => 'Planta eliminada exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la planta: ' . $e->getMessage()], 500);
        }
    }

    public function releaseRelatedRecords($id)
{
    try {
        // Verificar si la planta existe
        $planta = DB::table('Cat_Plantas')->where('Id_Planta', $id)->first();
    
        if (!$planta) {
            return response()->json(['message' => 'Planta no encontrada'], 404);
        }

        // Eliminar usuarios asignados a la planta
        DB::table('Cat_Usuarios')->where('Id_Planta', $id)->delete();

        // Eliminar empleados asignados a la planta
        DB::table('Cat_Empleados')->where('Id_Planta', $id)->delete();

        // Eliminar permisos asignados a la planta
        DB::table('Ctrl_Permisos_x_Area')->where('Id_Planta', $id)->delete();

        // Eliminar artículos asignados a la planta
        DB::table('Cat_Articulos')->where('Id_Planta', $id)->delete();

        // Eliminar máquinas asignadas a la planta
        DB::table('Ctrl_Mquinas')->where('Id_Planta', $id)->delete();

        return response()->json(['message' => 'Registros relacionados eliminados exitosamente']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al eliminar los registros relacionados: ' . $e->getMessage()], 500);
    }
}


    public function destroyAdmin($id)
    {
        try {
            // Busca el administrador por su ID
            $admin = DB::table('Cat_Usuarios_Administradores')->where('Id_Usuario_Admon', $id)->first();

            if (!$admin) {
                return response()->json(['message' => 'Administrador no encontrado'], 404);
            }

            // Eliminar el registro
            DB::table('Cat_Usuarios_Administradores')->where('Id_Usuario_Admon', $id)->delete();

            return response()->json(['message' => 'Administrador eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el administrador: ' . $e->getMessage()], 500);
        }
    }
    
    public function getAdministradores()
    {
        $administradores = DB::table('Cat_Usuarios_Administradores as adm')
        ->select(
            DB::raw("CONCAT(adm.Txt_Nombre, ' ', adm.Txt_ApellidoP, ' ', adm.Txt_ApellidoM) as NombreCompleto"),
            'adm.Id_Usuario_Admon as id',
            'adm.Nick as NombreUsuario',
            'adm.Txt_Estatus',
            'adm.Fecha_Alta',
            'adm.Fecha_Modificacion',
            'adm.Fecha_Baja',
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = adm.Id_Usuario_Admon_Alta) as UsuarioAlta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = adm.Id_Usuario_Admon_Modificacion) as UsuarioModificacion"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = adm.Id_Usuario_Admon_Baja) as UsuarioBaja")
        )
        ->get();

        return DataTables::of($administradores)->make(true);
    }

    public function getUsuarios()
    {
        $administradores = DB::table('Cat_Usuarios as usr')
        ->select(
            DB::raw("CONCAT(usr.Txt_Nombre, ' ', usr.Txt_ApellidoP, ' ', usr.Txt_ApellidoM) as NombreCompleto"),
            'usr.Id_Usuario as id',
            'usr.Id_Planta as planta', //Seleccionar la el nombre de la planta
            'usr.Nick_Usuario as NombreUsuario',
            'usr.Txt_Puesto',
            'usr.Txt_Estatus',
            'usr.Fecha_Alta',
            'usr.Fecha_Modificacion',
            'usr.Fecha_Baja',
            DB::raw("(SELECT Txt_Nombre_Planta FROM Cat_Plantas WHERE Id_Planta = usr.Id_Planta) as Nombre_Planta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = usr.Id_Usuario_Admon_Alta) as UsuarioAlta"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = usr.Id_Usuario_Admon_Modificacion) as UsuarioModificacion"),
            DB::raw("(SELECT Nick FROM Cat_Usuarios_Administradores WHERE Id_Usuario_Admon = usr.Id_Usuario_Admon_Baja) as UsuarioBaja")
        )
        ->get();

        return DataTables::of($administradores)->make(true);
    }

    public function updateEstatus(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
        $adminId = $request->id;
        $nuevoEstatus = $request->nuevoEstatus;
        $usuarioModificador = $_SESSION['usuario']->Id_Usuario_Admon; // Obtenemos el usuario actual desde la sesión

        // Verificamos si el administrador existe
        $admin = DB::table('Cat_Usuarios_Administradores')->where('Id_Usuario_Admon', $adminId)->first();
        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Administrador no encontrado'], 404);
        }

        // Actualizamos los campos dependiendo del nuevo estatus
        if ($nuevoEstatus == 'Alta') {
            DB::table('Cat_Usuarios_Administradores')
                ->where('Id_Usuario_Admon', $adminId)
                ->update([
                    'Txt_Estatus' => 'Alta',
                    'Fecha_Modificacion' => now(),
                    'Fecha_Baja' => null,
                    'Id_Usuario_Admon_Modificacion' => $usuarioModificador,
                    'Id_Usuario_Admon_Baja' => null,
                ]);
        } else if ($nuevoEstatus == 'Baja') {
            DB::table('Cat_Usuarios_Administradores')
                ->where('Id_Usuario_Admon', $adminId)
                ->update([
                    'Txt_Estatus' => 'Baja',
                    'Fecha_Baja' => now(),
                    'Id_Usuario_Admon_Baja' => $usuarioModificador,
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updateEstatusUser(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
        $userId = $request->id;
        $nuevoEstatus = $request->nuevoEstatus;
        $usuarioModificador = $_SESSION['usuario']->Id_Usuario_Admon; // Obtenemos el usuario actual desde la sesión

        // Verificamos si el administrador existe
        $user = DB::table('Cat_Usuarios')->where('Id_Usuario', $userId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Actualizamos los campos dependiendo del nuevo estatus
        if ($nuevoEstatus == 'Alta') {
            DB::table('Cat_Usuarios')
                ->where('Id_Usuario', $userId)
                ->update([
                    'Txt_Estatus' => 'Alta',
                    'Fecha_Modificacion' => now(),
                    'Fecha_Baja' => null,
                    'Id_Usuario_Admon_Modificacion' => $usuarioModificador,
                    'Id_Usuario_Admon_Baja' => null,
                ]);
        } else if ($nuevoEstatus == 'Baja') {
            DB::table('Cat_Usuarios')
                ->where('Id_Usuario', $userId)
                ->update([
                    'Txt_Estatus' => 'Baja',
                    'Fecha_Baja' => now(),
                    'Id_Usuario_Admon_Baja' => $usuarioModificador,
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updateEstatusPlanta(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
        $plantaId = $request->id;
        $nuevoEstatus = $request->nuevoEstatus;
        $usuarioModificador = $_SESSION['usuario']->Id_Usuario_Admon; // Obtenemos el usuario actual desde la sesión

        // Verificamos si la planta existe
        $user = DB::table('Cat_Plantas')->where('Id_Planta', $plantaId)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Planta no encontrada'], 404);
        }

        // Actualizamos los campos dependiendo del nuevo estatus
        if ($nuevoEstatus == 'Alta') {
            DB::table('Cat_Plantas')
                ->where('Id_Planta', $plantaId)
                ->update([
                    'Txt_Estatus' => 'Alta',
                    'Fecha_Modificacion' => now(),
                    'Fecha_Baja' => null,
                    'Id_Usuario_Admon_Modificacion' => $usuarioModificador,
                    'Id_Usuario_Admon_Baja' => null,
                ]);
        } else if ($nuevoEstatus == 'Baja') {
            DB::table('Cat_Plantas')
                ->where('Id_Planta', $plantaId)
                ->update([
                    'Txt_Estatus' => 'Baja',
                    'Fecha_Baja' => now(),
                    'Id_Usuario_Admon_Baja' => $usuarioModificador,
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Estatus actualizado correctamente.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function agregarAdministrador(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
    // Validar los datos entrantes
    $request->validate([
        'nombre' => 'required|string|max:255',
        'apellidoP' => 'required|string|max:255',
        'apellidoM' => 'nullable|string|max:255',
        'nick' => 'required|string|max:255|unique:Cat_Usuarios_Administradores',
        'password' => 'required|string|min:6', // Puedes ajustar la longitud mínima según tus requisitos
    ]);

    // Insertar el nuevo administrador en la base de datos
    DB::table('Cat_Usuarios_Administradores')->insert([
        'Txt_Nombre' => $request->nombre,
        'Txt_ApellidoP' => $request->apellidoP,
        'Txt_ApellidoM' => $request->apellidoM,
        'Nick' => $request->nick,
        'Contrasenia' => $request->password, // Asegúrate de hash la contraseña
        'Txt_Estatus' => "Alta",
        'Fecha_Alta' => now(),
        'Id_Usuario_Admon_Alta' => $_SESSION['usuario']->Id_Usuario_Admon, // Cambia esto según tu lógica
    ]);

    return response()->json(['message' => 'Administrador agregado con éxito']);
} catch (\Exception $e) {
    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
}
}

public function guardarUsuario(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    try {
    // Validar los datos entrantes
    $request->validate([
        'planta' => 'required',
        'puesto' => 'required',
        'nombre' => 'required|string|max:255',
        'apellidoP' => 'required|string|max:255',
        'apellidoM' => 'nullable|string|max:255',
        'nick' => 'required|string|max:255|unique:Cat_Usuarios_Administradores',
        'password' => 'required|string|min:6', // Puedes ajustar la longitud mínima según tus requisitos
    ]);

    // Insertar el nuevo administrador en la base de datos
    DB::table('Cat_Usuarios')->insert([
        'Txt_Nombre' => $request->nombre,
        'Txt_ApellidoP' => $request->apellidoP,
        'Txt_ApellidoM' => $request->apellidoM,
        'Txt_Puesto' => $request->puesto,
        'Id_Planta' => $request->planta,
        'Nick_Usuario' => $request->nick,
        'Contrasenia' => $request->password, // Asegúrate de hash la contraseña
        'Txt_Estatus' => "Alta",
        'Fecha_Alta' => now(),
        'Id_Usuario_Admon_Alta' => $_SESSION['usuario']->Id_Usuario_Admon, // Cambia esto según tu lógica
    ]);

    return response()->json(['message' => 'Usuario agregado con éxito']);
} catch (\Exception $e) {
    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
}
}

public function guardarPlanta(Request $request) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    try {
        // Validar los datos entrantes
        $request->validate([
            'txtSitio' => 'required|string|max:255',
            'txtCodigoCliente' => 'required|string|max:255',
            'txtNombrePlanta' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de la imagen (opcional)
        ]);

        // Manejar la carga de la imagen si está presente
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Verificar si el archivo es válido
            if ($request->file('image')->isValid()) {
                // Mover la imagen al directorio 'public/Images/Plantas'
                $imagePath = $request->file('image')->move(public_path('/Images/Plantas'), $request->file('image')->getClientOriginalName());
            } else {
                return response()->json(['success' => false, 'message' => 'El archivo no es válido.'], 400);
            }
        }

        // Insertar el nuevo registro en la base de datos
        DB::table('Cat_Plantas')->insert([
            'Txt_Nombre_Planta' => $request->txtNombrePlanta,
            'Txt_Codigo_Cliente' => $request->txtCodigoCliente,
            'Txt_Sitio' => $request->txtSitio,
            'Txt_Estatus' => "Alta",
            'Fecha_Alta' => now(),
            'Id_Usuario_Admon_Alta' => $_SESSION['usuario']->Id_Usuario_Admon,
            'Ruta_Imagen' => $imagePath ? '/Images/Plantas/' . $request->file('image')->getClientOriginalName() : null, // Guardar la ruta de la imagen en la base de datos si existe
        ]);

        return response()->json(['message' => 'Planta agregada con éxito']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updatePlanta(Request $request) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    try {
        // Encuentra el empleado por Id_Empleado
        $empleado = DB::table('Cat_Empleados')->where('Id_Empleado', $Id_Empleado)->first();

        if ($empleado) {
            Log::info('Empleado encontrado: ' . json_encode($empleado));

            // Elimina al empleado
            DB::table('Cat_Empleados')->where('Id_Empleado', $Id_Empleado)->delete();

            // Devuelve una respuesta exitosa
            return response()->json(['message' => 'Empleado eliminado con éxito.'], 200);
        } else {
            // Empleado no encontrado
            Log::warning("Empleado con Id_Empleado {$Id_Empleado} no encontrado.");
            return response()->json(['message' => 'Empleado no encontrado.'], 404);
        }
    } catch (\Exception $e) {
        // Registra el error en los logs
        Log::error('Error al eliminar el empleado:', [
            'Id_Empleado' => $Id_Empleado,
            'error' => $e->getMessage(),
        ]);

        // Devuelve una respuesta con el mensaje de error
        return response()->json([
            'message' => 'No se pudo eliminar el empleado.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

        public function PlantaView($lang, $id)
        {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Verificar si la planta existe
            $planta = DB::table('Cat_Plantas')->where('Id_Planta', $id)->first();

            if (!$planta) {
                return response()->json(['message' => 'Planta no encontrada'], 404);
            }
            $areas = DB::table('Cat_Area')
                ->where('Id_Planta', $id)
                ->get();

            $articulos = DB::table('Cat_Articulos')
                        ->where('Id_Planta', $id)
                        ->get();
                        //dd($articulos);

            // Obtener las máquinas vending asociadas a la planta
            $vendings = DB::table('Ctrl_Mquinas')->where('Id_Planta', $id)->get();

            

            // Pasar todos los datos a la vista
            return view('administracion.plantas.infoplanta', compact('planta', 'vendings','areas','articulos'));
        }

        public function TablasPlant($id){
            // Comprobación si es una solicitud AJAX para DataTables
            if (request()->ajax()) {
                $tableType = request('table'); // Captura el tipo de tabla solicitado
                $areas=array();

                
                
                if ($tableType === 'areas') {
                    
                    $dataareas = DB::table('Cat_Area')->select('Id_Area','Id_Planta','Txt_Nombre','Txt_Estatus','Fecha_Alta','Fecha_Modificacion','Fecha_Baja')->where('Id_Planta',$id)->get();
                foreach ($dataareas as $area) {
                    $ModFecha = Date::parse($area->Fecha_Alta);
                    $AltaFecha = Date::parse($area->Fecha_Modificacion);
                    $AFecha = $AltaFecha->format('l, j F Y H:i:s');
                    $MFecha = $ModFecha->format('l, j F Y H:i:s');
                    $area->AFecha = $AFecha;
                    $area->MFecha = $MFecha;
                    array_push($areas, $area);
                }
                    return DataTables::of($dataareas)->make(true);
                }

                if ($tableType === 'permisos') {
                    $permisos = DB::table('Ctrl_Permisos_x_Area')
                                    ->join('Cat_Area', 'Cat_Area.Id_Area', '=', 'Ctrl_Permisos_x_Area.Id_Area')
                                    ->where('Cat_Area.Id_Planta', $id)
                                    ->select('Ctrl_Permisos_x_Area.Id_Permiso', 'Cat_Area.Txt_Nombre', 'Ctrl_Permisos_x_Area.Frecuencia', 'Ctrl_Permisos_x_Area.Cantidad', 'Ctrl_Permisos_x_Area.Status');
                    return DataTables::of($permisos)->make(true);
                }

                if ($tableType === 'empleados') {
                    $empleados = DB::table('Cat_Empleados')
                                    ->where('Id_Planta', $id)
                                    ->select('Id_Empleado', 'Nombre', 'APaterno', 'AMaterno', 'No_Empleado','Nip','No_Tarjeta','Tipo_Acceso','Fecha_alta','Fecha_Modificacion','Fecha_Baja','Id_Usuario_Alta','Id_Usuario_Modificacion','Id_Usuario_Baja');
                    return DataTables::of($empleados)->make(true);
                }
            }
        }

        ////////////////////////////////////////// FUNCIONES DE AREA /////////////////////////////////////////

        public function updateStatusArea(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    // Obtén los datos del request
    $idArea = $request->input('id_area');
    $newStatus = $request->input('new_status');
    $plantaId = $request->input('idPlanta'); // Obtiene el Id de Planta desde la sesión
    Log::info('Planta:'.$plantaId);

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
    $currentDate = now(); // Obtiene la fecha actual
    $plantaId = $request->input('idPlanta'); // Obtiene el Id de Planta 
    Log::info('Planta:'.$plantaId);

    // Verificar si el área ya existe en la misma planta
    $existingArea = DB::table('Cat_Area')
        ->where('Id_Planta', $plantaId)
        ->where('Txt_Nombre', $newName)
        ->first();

    if ($existingArea) {
        return response()->json(['success' => false, 'message' => 'El área ya existe.']);
    }

    // Insertar el nuevo área en la base de datos
    $idArea = DB::table('Cat_Area')->insertGetId([
        'Id_Planta' => $plantaId,
        'Txt_Nombre' => $newName,
        'Fecha_Alta' => $currentDate,
        'Txt_Estatus' => 'Alta',
        'Fecha_Modificacion' => null,
        'Fecha_Baja' => null,
        'Id_Usuario_Alta' => 1,
        'Id_Usuario_Modificacion' => null,
        'Id_Usuario_Baja' => null
    ]);

    if ($idArea) {
        // Obtener todos los artículos de la misma planta
        $articulos = DB::table('Cat_Articulos')
            ->where('Id_Planta', $plantaId)
            ->where('Txt_Estatus', 'Alta')
            ->get();

        foreach ($articulos as $articulo) {
            // Verificar si ya existe un permiso para este área y artículo
            $existingPermiso = DB::table('Ctrl_Permisos_x_Area')
                ->where('Id_Area', $idArea)
                ->where('Id_Articulo', $articulo->Id_Articulo)
                ->first();

            if (!$existingPermiso) {
                // Insertar un nuevo permiso en la tabla Ctrl_Permisos_x_Area
                DB::table('Ctrl_Permisos_x_Area')->insert([
                    'Id_Area' => $idArea,
                    'Id_Articulo' => $articulo->Id_Articulo,
                    'Frecuencia' => 0,
                    'Cantidad' => 0,
                    'Id_Planta' => $plantaId,
                    'Status' => 'Alta',
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Área y permisos creados correctamente.']);
    } else {
        return response()->json(['success' => false, 'message' => 'Error al crear el área.']);
    }
}

public function generateMissingPermissions(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    $plantaId = $request->input('idPlanta'); // Obtiene el Id de Planta desde la sesión

    // Obtener todas las áreas de la planta
    $areas = DB::table('Cat_Area')
        ->where('Id_Planta', $plantaId)
        ->where('Txt_Estatus', 'Alta')
        ->get();

    // Obtener todos los artículos de la planta
    $articulos = DB::table('Cat_Articulos')
        ->where('Id_Planta', $plantaId)
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

public function exportExcelAreas(Request $request) {
    $idPlanta = $request->query('idPlanta'); // Obtener 'idPlanta' desde la URL
    return Excel::download(new AreasExportAdmin($idPlanta), 'areas.xlsx');
}

////////////////////////////////////////// FUNCIONES DE PERMISOS //////////////////////////////////////////////////////
public function getPermisosArticulos(Request $request, $idPlanta)
{
    if (session_status() == PHP_SESSION_NONE) {
session_start();
}
    try {
            if (isset($_SESSION['usuario'])) {
                
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

public function addPermission(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
session_start();
}
    $idPlanta = $request->input('idPlanta');
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

public function checkPermission(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $idPlanta = $request->input('idPlanta');
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
        // Puedes personalizar el nombre del archivo que se descargará
        $fileName = 'Reporte_Permisos_' . now()->format('Ymd_His') . '.xlsx';
        $idPlanta = $request->query('idPlanta'); // Obtener 'idPlanta' desde la URL

        // Generar y descargar el archivo Excel
        return Excel::download(new PermisosExportAdmin($idPlanta), $fileName);
    }

    // FUNCIONES DE EMPLEADOS

    public function getDataEmpleados($idPlanta)
    {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $data=array();
        $Empleados = DB::table('Cat_Empleados')->select('Id_Empleado','Nombre','APaterno','AMaterno','No_Empleado','Nip','No_Tarjeta','Id_Area','Tipo_Acceso','Fecha_alta','Fecha_Modificacion','Txt_Estatus')->where('Id_Planta',$idPlanta)->get();
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

    public function storeemployee(Request $request) {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $usuario = '1';

        $validated = $request->validate([
            'no_empleado' => 'required|unique:Cat_Empleados,No_Empleado',
            'nip' => 'nullable|integer|max:9999',
            'no_tarjeta' => 'nullable|integer|unique:Cat_Empleados,No_Tarjeta',
            'nombre' => 'required|string|max:255',
            'apaterno' => 'required|string|max:255',
            'amaterno' => 'nullable|string|max:255',
            'area' => 'required|exists:Cat_Area,Id_Area',
            'idPlanta' => 'required'

        ]);
        

        // Aplicar valores por defecto si no se proporcionan
        $nip = $validated['nip'] ?? '1234';
        $no_tarjeta = $validated['no_tarjeta'] ?? '';
        $amaterno = $validated['amaterno'] ?? '';
    
        DB::table('Cat_Empleados')->insert([
            'No_Empleado' => $validated['no_empleado'],
            'Id_Planta' => $validated['idPlanta'],
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

    public function exportCSV(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $idPlanta = $request->query('idPlanta'); // Obtener 'idPlanta' desde la URL
    $filename = "empleados_" . date('Ymd') . ".csv";
    $empleados = DB::table('Cat_Empleados')
        ->select('No_Empleado', 'Nip', 'No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'Id_Area', 'Txt_Estatus', 'Tipo_Acceso')
        ->where('Id_Planta', $idPlanta)
        ->get();

    $headers = array(
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    );

    $columns = array('No_Empleado', 'Nip', 'No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'NArea', 'Txt_Estatus');

    $callback = function () use ($empleados, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($empleados as $empleado) {
            $area = DB::table('Cat_Area')->where('Id_Area', $empleado->Id_Area)->value('Txt_Nombre');

            // Convertir los valores numéricos a cadenas con ceros a la izquierda
            $data = array(
                (string)$empleado->No_Empleado,  // Respetar ceros a la izquierda
                (string)$empleado->Nip,
                (string)$empleado->No_Tarjeta,
                $empleado->Nombre,
                $empleado->APaterno,
                $empleado->AMaterno,
                $area,
                $empleado->Txt_Estatus,
            );

            

            fputcsv($file, $data);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    public function importCSV(Request $request) {
        if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
        $id_planta = $request->query('idPlanta'); // Obtener 'idPlanta' desde la URL
        $usuario = 1;
    
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

    public function exportExcel(Request $request) {
        $idPlanta = $request->query('idPlanta'); // Obtener 'idPlanta' desde la UR
        return Excel::download(new EmpleadosExportAdmin($idPlanta), 'empleados.xlsx');
    }


}
