<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmpleadosExportAdmin;
use App\Exports\PermisosExportAdmin;
use App\Exports\AreasExportAdmin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function Home(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.home');
    }
    public function Dispositivos(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.vendings.dispositivos');
    }
    public function Plantas(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.plantas.plantas');
    }
    public function Planograma(Request $request, $lang, $id) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
    
        // Obtener el planograma
        $planograma = DB::table('Configuracion_Maquina')
            ->where('Id_Maquina', $id)
            ->leftJoin('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Configuracion_Maquina.Id_Configuracion',
                'Configuracion_Maquina.Id_Articulo',
                'Configuracion_Maquina.Cantidad_Max',
                'Configuracion_Maquina.Cantidad_Min',
                'Configuracion_Maquina.Seleccion',
                'Configuracion_Maquina.Num_Charola',
                'Cat_Articulos.Txt_Codigo',
                'Cat_Articulos.Txt_Descripcion'
            )
            ->get()
            ->groupBy('Num_Charola');
    
        if ($planograma->isEmpty()) {
            return response()->json(['message' => 'Vending no encontrada'], 404);
        }
    
        // Si la solicitud incluye un término de búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $articulos = DB::table('Cat_Articulos')
                ->select('Id_Articulo', 'Txt_Descripcion', 'Txt_Codigo')
                ->where('Txt_Descripcion', 'LIKE', "%$search%")
                ->orWhere('Txt_Codigo', 'LIKE', "%$search%")
                ->limit(5) // Limitar a 5 resultados
                ->get();
    
            return response()->json($articulos);
        }
    
        // Obtener 4 artículos aleatorios para mostrar inicialmente
        $articulos = DB::table('Cat_Articulos')
            ->select('Id_Articulo', 'Txt_Descripcion', 'Txt_Codigo')
            ->inRandomOrder()
            ->limit(4)
            ->get();
    
        return view('administracion.vendings.planograma')
            ->with('planograma', $planograma)
            ->with('articulos', $articulos);
    }
    public function Articulos(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
        return view('administracion.articulos.articulos');
    }
    public function Vendings(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;
         // Consulta para obtener todas las plantas
            $plantas = DB::select("
            SELECT [Id_Planta],
                [Txt_Nombre_Planta],
                [Txt_Codigo_Cliente],
                [Txt_Sitio],
                [Txt_Estatus],
                [Fecha_Alta],
                [Fecha_Modificacion],
                [Fecha_Baja],
                [Id_Usuario_Admon_Alta],
                [Id_Usuario_Admon_Modificacion],
                [Id_Usuario_Admon_Baja],
                [Ruta_Imagen]
            FROM [Vending_Machine].[dbo].[Cat_Plantas]
        ");

        // Consulta para obtener dispositivos no asignados a ninguna máquina
        $dispositivosNoAsignados = DB::select("
            SELECT d.[Id_Dispositivo],
                d.[Txt_Serie_Dispositivo],
                d.[Txt_Estatus],
                d.[Fecha_Alta],
                d.[Fecha_Modificacion],
                d.[Fecha_Baja],
                d.[Id_Usuario_Admon_Alta],
                d.[Id_Usuario_Admon_Modificacion],
                d.[Id_Usuario_Admon_Baja]
            FROM [Vending_Machine].[dbo].[Cat_Dispositivo] d
            LEFT JOIN [Vending_Machine].[dbo].[Ctrl_Mquinas] m
            ON d.[Id_Dispositivo] = m.[Id_Dispositivo]
            WHERE m.[Id_Dispositivo] IS NULL
        ");
        return view('administracion.vendings.vendings', [
            'plantas' => $plantas,
            'dispositivosNoAsignados' => $dispositivosNoAsignados,
        ]);
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
            // Verificar si hay maquinas asignadas a la planta
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
        'Txt_Rol' => 'cliente',
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

        // Insertar el nuevo registro en la base de datos sin la imagen
        $idPlanta = DB::table('Cat_Plantas')->insertGetId([
            'Txt_Nombre_Planta' => $request->txtNombrePlanta,
            'Txt_Codigo_Cliente' => $request->txtCodigoCliente,
            'Txt_Sitio' => $request->txtSitio,
            'Txt_Estatus' => "Alta",
            'Fecha_Alta' => now(),
            'Id_Usuario_Admon_Alta' => $_SESSION['usuario']->Id_Usuario_Admon,
            'Ruta_Imagen' => null, // Inicialmente vacío
        ]);

        // Manejar la carga de la imagen si está presente
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                // Generar el nuevo nombre de archivo basado en Id_Planta
                $newImageName = $idPlanta . '.' . $request->file('image')->getClientOriginalExtension();

                // Mover la imagen al directorio 'public/Images/Plantas' con el nuevo nombre
                $imagePath = $request->file('image')->move(public_path('/Images/Plantas'), $newImageName);

                // Actualizar la ruta de la imagen en el registro
                DB::table('Cat_Plantas')->where('Id_Planta', $idPlanta)->update([
                    'Ruta_Imagen' => '/Images/Plantas/' . $newImageName,
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'El archivo no es válido.'], 400);
            }
        }

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
        // Validar los datos
        Log::info('Iniciando el proceso de actualización de planta.');

        $request->validate([
            'txtSitio' => 'required|string|max:255',
            'txtCodigoCliente' => 'required|string|max:255',
            'txtNombrePlanta' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de la imagen (opcional)
        ]);

        // Obtener los datos actuales de la planta (si existe una imagen anterior)
        $planta = DB::table('Cat_Plantas')->where('Id_Planta', $request->plantId)->first();
        Log::info('Datos de la planta obtenidos: ', ['planta' => $request]);

        // Si el archivo de imagen está presente, guardarlo en el directorio adecuado
        $imagePath = null;
        if ($request->hasFile('image')) {
            Log::info('Se ha detectado una imagen para actualizar.');

            // Eliminar la imagen anterior si existe
            if ($planta && $planta->Ruta_Imagen && file_exists(public_path('/Images/Plantas/' . $planta->Ruta_Imagen))) {
                Log::info('Eliminando la imagen anterior: ' . $planta->Ruta_Imagen);
                unlink(public_path('/Images/Plantas/' . $planta->Ruta_Imagen)); // Eliminar la imagen anterior
            } else {
                Log::info('No se encontró una imagen anterior para eliminar.');
            }

            // Guardar la nueva imagen en el directorio 'public/Images/Plantas'
            $imagePath = $request->file('image')->move(public_path('/Images/Plantas'), $request->file('image')->getClientOriginalName());
            Log::info('Imagen guardada con éxito en: ' . $imagePath);
        }

        // Datos para actualizar
        $dataToUpdate = [
            'Txt_Nombre_Planta' => $request->txtNombrePlanta,
            'Txt_Codigo_Cliente' => $request->txtCodigoCliente,
            'Txt_Sitio' => $request->txtSitio,
            'Txt_Estatus' => "Alta",
            'Fecha_Modificacion' => now(),
            'Id_Usuario_Admon_Modificacion' => $_SESSION['usuario']->Id_Usuario_Admon,
        ];

        // Si se cargó una nueva imagen, agregar la ruta de la imagen al array de datos
        if ($imagePath) {
            $dataToUpdate['Ruta_Imagen'] = '/Images/Plantas/' . $request->file('image')->getClientOriginalName();
            Log::info('Ruta de la imagen añadida a los datos para actualizar: ' . $dataToUpdate['Ruta_Imagen']);
        }

        // Actualizar los datos en la base de datos
        DB::table('Cat_Plantas')
            ->where('Id_Planta', $request->plantId)
            ->update($dataToUpdate);
        Log::info('Planta actualizada con éxito en la base de datos.');

        return response()->json(['message' => 'Planta actualizada con éxito']);
    } catch (\Exception $e) {
        Log::error('Error al actualizar la planta: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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

    $plantaId = $request->input('idPlanta');

    // 1️⃣ Obtener todas las áreas activas de la planta
    $areas = DB::table('Cat_Area')
        ->where('Id_Planta', $plantaId)
        ->where('Txt_Estatus', 'Alta')
        ->get();

    // 2️⃣ Obtener las máquinas vending de la planta
    $maquinas = DB::table('Ctrl_Mquinas')
        ->where('Id_Planta', $plantaId)
        ->pluck('Id_Maquina');

    if ($maquinas->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No hay máquinas vending en esta planta.']);
    }

    // 3️⃣ Obtener los artículos que están en esas máquinas (sin repetir y eliminando NULLs)
    $articulos = DB::table('Configuracion_Maquina')
        ->whereIn('Id_Maquina', $maquinas)
        ->whereNotNull('Id_Articulo') // ⚠️ Filtrar artículos NULL
        ->distinct()
        ->pluck('Id_Articulo')
        ->filter() // ⚠️ Asegurar que no haya valores vacíos o NULL
        ->toArray(); // Convertir a array para facilidad de manejo

    if (empty($articulos)) {
        return response()->json(['success' => false, 'message' => 'No hay artículos en las máquinas vending de esta planta.']);
    }

    // 4️⃣ Obtener permisos actuales para esta planta
    $permisosActuales = DB::table('Ctrl_Permisos_x_Area')
        ->where('Id_Planta', $plantaId)
        ->get(['Id_Area', 'Id_Articulo']);

    // Convertir a un array asociativo para fácil búsqueda
    $permisosExistentes = [];
    foreach ($permisosActuales as $permiso) {
        $permisosExistentes[$permiso->Id_Area][$permiso->Id_Articulo] = true;
    }

    // 5️⃣ Insertar permisos faltantes
    $permisosNuevos = [];
    foreach ($areas as $area) {
        foreach ($articulos as $idArticulo) {
            if (!isset($permisosExistentes[$area->Id_Area][$idArticulo])) {
                $permisosNuevos[] = [
                    'Id_Area' => $area->Id_Area,
                    'Id_Articulo' => $idArticulo,
                    'Frecuencia' => 0,
                    'Cantidad' => 0,
                    'Id_Planta' => $plantaId,
                    'Status' => 'Alta',
                ];
            }
        }
    }

    if (!empty($permisosNuevos)) {
        DB::table('Ctrl_Permisos_x_Area')->insert($permisosNuevos);
    }

    // 6️⃣ Eliminar permisos que ya no deberían existir
    $permisosAEliminar = DB::table('Ctrl_Permisos_x_Area')
        ->where('Id_Planta', $plantaId)
        ->whereNotIn('Id_Articulo', $articulos) // Si el artículo no está en las máquinas vending
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Permisos actualizados correctamente.',
        'nuevos' => count($permisosNuevos),
        'eliminados' => $permisosAEliminar,
    ]);
}

public function generateAllMissingPermissions(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $plantaId = $request->input('idPlanta');

    // 1️⃣ Obtener todas las áreas activas de la planta
    $areas = DB::table('Cat_Area')
        ->where('Id_Planta', $plantaId)
        ->where('Txt_Estatus', 'Alta')
        ->get();

    // 2️⃣ Obtener las máquinas vending de la planta
    $maquinas = DB::table('Ctrl_Mquinas')
        ->where('Id_Planta', $plantaId)
        ->pluck('Id_Maquina');

    if ($maquinas->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No hay máquinas vending en esta planta.']);
    }

    // 3️⃣ Obtener los artículos que están en esas máquinas (sin repetir y eliminando NULLs)
    $articulos = DB::table('Configuracion_Maquina')
        ->whereIn('Id_Maquina', $maquinas)
        ->whereNotNull('Id_Articulo') // ⚠️ Filtrar artículos NULL
        ->distinct()
        ->pluck('Id_Articulo')
        ->filter() // ⚠️ Asegurar que no haya valores vacíos o NULL
        ->toArray(); // Convertir a array para facilidad de manejo

    if (empty($articulos)) {
        return response()->json(['success' => false, 'message' => 'No hay artículos en las máquinas vending de esta planta.']);
    }

    // 4️⃣ Obtener permisos actuales para esta planta
    $permisosActuales = DB::table('Ctrl_Permisos_x_Area')
        ->where('Id_Planta', $plantaId)
        ->get(['Id_Area', 'Id_Articulo']);

    // Convertir a un array asociativo para fácil búsqueda
    $permisosExistentes = [];
    foreach ($permisosActuales as $permiso) {
        $permisosExistentes[$permiso->Id_Area][$permiso->Id_Articulo] = true;
    }

    // 5️⃣ Insertar permisos faltantes
    $permisosNuevos = [];
    foreach ($areas as $area) {
        foreach ($articulos as $idArticulo) {
            if (!isset($permisosExistentes[$area->Id_Area][$idArticulo])) {
                $permisosNuevos[] = [
                    'Id_Area' => $area->Id_Area,
                    'Id_Articulo' => $idArticulo,
                    'Frecuencia' => 0,
                    'Cantidad' => 0,
                    'Id_Planta' => $plantaId,
                    'Status' => 'Alta',
                ];
            }
        }
    }

    if (!empty($permisosNuevos)) {
        DB::table('Ctrl_Permisos_x_Area')->insert($permisosNuevos);
    }

    // 6️⃣ Eliminar permisos que ya no deberían existir
    $permisosAEliminar = DB::table('Ctrl_Permisos_x_Area')
        ->where('Id_Planta', $plantaId)
        ->whereNotIn('Id_Articulo', $articulos) // Si el artículo no está en las máquinas vending
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Permisos actualizados correctamente.',
        'nuevos' => count($permisosNuevos),
        'eliminados' => $permisosAEliminar,
    ]);
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

//Filtrar permisos por Area
public function filtrarPermisosPorArea(Request $request, $idPlanta, $idArea)
{
    
        try {
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
                ->where('Ctrl_Permisos_x_Area.Id_Area', $idArea)
                ->where('Cat_Area.Id_Planta', $idPlanta)
                ->get();
    
            return DataTables::of($data)->make(true);
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
                $no_tarjeta = !empty($row[2]) ? $this->sanitizeString($row[2]) : null;
                $nombre = !empty($row[3]) ? $this->sanitizeString($row[3]) : null;
                $a_paterno = !empty($row[4]) ? $this->sanitizeString($row[4]) : null;
                $a_materno = !empty($row[5]) ? $this->sanitizeString($row[5]) : '';
                $n_area = !empty($row[6]) ? $this->sanitizeString($row[6]) : null;
                $estatus = !empty($row[7]) ? $this->sanitizeString($row[7]) : 'Alta';

                if (is_null($no_empleado)) {
                    $status = 'error';
                    $message = "El campo No_Empleado está vacío. No se ha importado este registro.";
                    break;
                }

                if (empty($nombre) || empty($a_paterno)) {
                    $status = 'error';
                    $message = "El campo Nombre y/o Apellido Paterno está vacío para el empleado '$no_empleado'. No se ha importado este registro.";
                    break;
                }

                if (is_null($n_area)) {
                    $status = 'error';
                    $message = "El campo de área está vacío para el empleado '$no_empleado'. No se ha importado este registro.";
                    break;
                } else {
                    $id_area = DB::table('Cat_Area')->where('Txt_Nombre', $n_area)->value('Id_Area');

                    if (!$id_area) {
                        $status = 'error';
                        $message = "El área '$n_area' no se encontró en la base de datos para el empleado '$no_empleado'. No se ha importado este registro.";
                        break;
                    }
                }

                $empleado = DB::table('Cat_Empleados')->where('No_Empleado', $no_empleado)->first();

                if ($empleado) {
                    // Actualizar empleado existente
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
                            $message = "El número de tarjeta '$no_tarjeta' ya está registrado para otro empleado. No se ha importado este registro.";
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

/**
 * Función para limpiar y convertir la codificación de caracteres a UTF-8
 */
private function sanitizeString($string) {
    return mb_convert_encoding(trim($string), 'UTF-8', 'auto'); 
}

    public function exportExcel(Request $request) {
        $idPlanta = $request->query('idPlanta'); // Obtener 'idPlanta' desde la UR
        return Excel::download(new EmpleadosExportAdmin($idPlanta), 'empleados.xlsx');
    }


    public function getArticulosDataTable(Request $request)
{
    $articulos = DB::table('Cat_Articulos AS ca')
        ->leftJoin('Cat_Usuarios AS cua', 'ca.Id_Usuario_Alta', '=', 'cua.Id_Usuario')
        ->leftJoin('Cat_Usuarios AS cub', 'ca.Id_Usuario_Baja', '=', 'cub.Id_Usuario')
        ->leftJoin('Cat_Usuarios AS cum', 'ca.Id_Usuario_Modificacion', '=', 'cum.Id_Usuario')
        ->select([
            'ca.Id_Articulo',
            'ca.Txt_Descripcion',
            'ca.Txt_Codigo',
            'ca.Txt_Codigo_Cliente',
            'ca.Txt_Estatus',
            'ca.Fecha_Alta',
            'ca.Fecha_Modificacion',
            'ca.Fecha_Baja',
            DB::raw("COALESCE(CONCAT(cua.Txt_Nombre, ' ', cua.Txt_ApellidoP, ' ', cua.Txt_ApellidoM), '') AS UsuarioAlta"),
            DB::raw("COALESCE(CONCAT(cub.Txt_Nombre, ' ', cub.Txt_ApellidoP, ' ', cub.Txt_ApellidoM), '') AS UsuarioBaja"),
            DB::raw("COALESCE(CONCAT(cum.Txt_Nombre, ' ', cum.Txt_ApellidoP, ' ', cum.Txt_ApellidoM), '') AS UsuarioModificacion"),
        ]);

    // Aplicar filtros personalizados
    if ($request->filled('descripcion')) {
        $articulos->where('ca.Txt_Descripcion', 'LIKE', '%' . $request->input('descripcion') . '%');
    }

    if ($request->filled('codigo')) {
        $articulos->where('ca.Txt_Codigo', 'LIKE', '%' . $request->input('codigo') . '%');
    }

    if ($request->filled('codigo_cliente')) {
        $articulos->where('ca.Txt_Codigo_Cliente', 'LIKE', '%' . $request->input('codigo_cliente') . '%');
    }

    return DataTables::of($articulos)
        ->addColumn('Imagen', function ($articulo) {
            return '<img src="https://172.31.1.1/imagenes/Catalogo/' . $articulo->Txt_Codigo . '.jpg" alt="Imagen" width="50" height="50">';
        })
        ->rawColumns(['Imagen'])
        ->make(true);
}


    public function cambiarEstatus(Request $request)
    {
        // Validar que los datos estén presentes
        $request->validate([
            'id' => 'required|exists:Cat_Articulos,Id_Articulo',
            'status' => 'required|in:Alta,Baja',
        ]);

        // Realizar la actualización con DB::table()
        $updated = DB::table('Cat_Articulos')
            ->where('Id_Articulo', $request->id)
            ->update(['Txt_Estatus' => $request->status]);

        // Verificar si la actualización fue exitosa
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Estatus cambiado con éxito']);
        }

        // Si no se pudo actualizar, retornar un error
        return response()->json(['success' => false, 'message' => 'Error al cambiar el estatus']);
    }

    public function storeArticulo(Request $request)
{
    // Validar los datos recibidos
    $request->validate([
        'Txt_Descripcion' => 'required|string|max:255',
        'Txt_Codigo' => 'required|string|max:50',
        'Txt_Codigo_Cliente' => 'required|string|max:50',
    ]);

    // Verificar si ya existe un artículo con el mismo Txt_Codigo
    $codigoExistente = DB::table('Cat_Articulos')
        ->where('Txt_Codigo', $request->Txt_Codigo)
        ->exists();

        if ($codigoExistente) {
            return response()->json([
                'success' => false,
                'message' => 'El código ingresado ya está registrado. Por favor, ingrese un código único.'
            ], 400); // Código de error 400 para "Bad Request"
        }

    // Obtener la fecha actual
    $fechaActual = Carbon::now();

    // Insertar el nuevo artículo en la base de datos
    DB::table('Cat_Articulos')->insert([
        'Txt_Descripcion' => $request->Txt_Descripcion,
        'Txt_Codigo' => $request->Txt_Codigo,
        'Txt_Codigo_Cliente' => $request->Txt_Codigo_Cliente,
        'Txt_Estatus' => 'Alta',  // El estatus es 'Alta' por defecto
        'Fecha_Alta' => $fechaActual, // Fecha actual
        'Fecha_Modificacion' => null,
        'Fecha_Baja' => null,
        'Id_Usuario_Alta' => 1, // Obtener el id del usuario autenticado
        'Id_Usuario_Modificacion' => null,
        'Id_Usuario_Baja' => null,
    ]);

    // Retornar respuesta en formato JSON
    return response()->json(['success' => true, 'message' => 'Artículo agregado con éxito']);
}


public function deleteArticulo($id)
{
    // Verificar si el artículo existe
    $articulo = DB::table('Cat_Articulos')->where('Id_Articulo', $id)->first();
    if (!$articulo) {
        return response()->json(['message' => 'Artículo no encontrado'], 404);
    }

    // Eliminar el artículo
    DB::table('Cat_Articulos')->where('Id_Articulo', $id)->delete();

    return response()->json(['message' => 'Artículo eliminado con éxito']);
}

public function editArticulo($id)
{
    // Obtener los datos del artículo
    $articulo = DB::table('Cat_Articulos')->where('Id_Articulo', $id)->first();

    if (!$articulo) {
        return response()->json(['message' => 'Artículo no encontrado'], 404);
    }

    return response()->json($articulo);
}

public function updateArticulo(Request $request, $id)
{
    // Validar los datos
    $validated = $request->validate([
        'Txt_Descripcion' => 'required|string|max:255',
        'Txt_Codigo' => 'required|string|max:50',
        'Txt_Codigo_Cliente' => 'nullable|string|max:50',
    ]);

    // Actualizar el artículo
    DB::table('Cat_Articulos')->where('Id_Articulo', $id)->update([
        'Txt_Descripcion' => $validated['Txt_Descripcion'],
        'Txt_Codigo' => $validated['Txt_Codigo'],
        'Txt_Codigo_Cliente' => $validated['Txt_Codigo_Cliente'],
        'Fecha_Modificacion' => now(),
        'Id_Usuario_Modificacion' => 1
    ]);

    return response()->json(['message' => 'Artículo actualizado con éxito']);
}
   
public function getVendingsData()
{
    // Comprobamos si la sesión está activa
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Obtenemos el Id_Usuario_Admon desde la sesión
    $userId = $_SESSION['usuario']->Id_Usuario_Admon;

    // Realizamos la consulta SQL con Facade DB
    // Realizamos la consulta SQL con Facade DB utilizando JOIN
     // Consulta para obtener los datos y agruparlos por planta
     $vendingsData = DB::table('Ctrl_Mquinas')
     ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
     ->select([
         'Cat_Plantas.Txt_Nombre_Planta',
         'Ctrl_Mquinas.Id_Maquina',
         'Ctrl_Mquinas.Id_Dispositivo',
         'Ctrl_Mquinas.Txt_Nombre',
         'Ctrl_Mquinas.Txt_Serie_Maquina',
         'Ctrl_Mquinas.Txt_Tipo_Maquina',
         'Ctrl_Mquinas.Txt_Estatus',
         'Ctrl_Mquinas.Capacidad',
         'Ctrl_Mquinas.Fecha_Alta',
         'Ctrl_Mquinas.Fecha_Modificacion',
         'Ctrl_Mquinas.Fecha_Baja',
         'Ctrl_Mquinas.Id_Usuario_Admon_Alta',
         'Ctrl_Mquinas.Id_Usuario_Admon_Modificacion',
         'Ctrl_Mquinas.Id_Usuario_Admon_Baja',
         'Cat_Plantas.Ruta_Imagen'
     ])
     ->get()
    ->groupBy('Txt_Nombre_Planta'); // Agrupamos por el nombre de la planta

    // Devolvemos los datos como JSON para que AJAX los consuma
    return response()->json($vendingsData);
}

public function changeStatusvm(Request $request)
{
    // Utilizamos el Facade DB para hacer la actualización directamente
    $maquina = DB::table('Ctrl_Mquinas') // Reemplaza 'Maquinas' con el nombre de la tabla correspondiente
                 ->where('Id_Maquina', $request->id_maquina)
                 ->first();

    if ($maquina) {
        // Cambiamos el estatus entre "Alta" y "Baja"
        $newStatus = $maquina->Txt_Estatus == 'Alta' ? 'Baja' : 'Alta';

        // Actualizamos el estatus usando DB Facade
        DB::table('Ctrl_Mquinas')
            ->where('Id_Maquina', $request->id_maquina)
            ->update(['Txt_Estatus' => $newStatus]);
    }

    return response()->json(['status' => 'success', 'new_status' => $newStatus]);
}

public function deletevm(Request $request)
{
    $hasConfig = DB::table('Configuracion_Maquina')
                   ->where('Id_Maquina', $request->id_maquina)
                   ->exists();

    if ($hasConfig) {
        return response()->json([
            'status' => 'error',
            'message' => 'Reasigne la configuración a otra vending antes de eliminarla.'
        ]);
    }

    // Si no tiene registros relacionados, procedemos a eliminar la máquina
    DB::table('Ctrl_Mquinas')->where('Id_Maquina', $request->id_maquina)->delete();

    return response()->json(['status' => 'success', 'message' => 'Máquina eliminada correctamente.']);
}

// Método para obtener los detalles de la máquina
public function getVendingMachine($id)
{
    // Obtener los datos de la máquina, incluyendo el dispositivo asignado
    $vendingMachine = DB::table('Ctrl_Mquinas')
        ->join('Cat_Dispositivo', 'Ctrl_Mquinas.Id_Dispositivo', '=', 'Cat_Dispositivo.Id_Dispositivo')
        ->where('Ctrl_Mquinas.Id_Maquina', $id)
        ->select('Ctrl_Mquinas.*', 'Cat_Dispositivo.Txt_Serie_Dispositivo', 'Ctrl_Mquinas.Id_Dispositivo')
        ->first();

    // Verificar si se encontró la máquina
    if ($vendingMachine) {
        return response()->json($vendingMachine);
    } else {
        return response()->json(['error' => 'Máquina no encontrada'], 404);
    }
}

public function updateVendingMachine(Request $request)
{
    try {
        // Validamos los datos del formulario
        $validated = $request->validate([
            'Txt_Nombre' => 'required|string|max:255',
            'Txt_Serie_Maquina' => 'required|string|max:255',
            'Txt_Tipo_Maquina' => 'required|string|max:255',
            'Txt_Estatus' => 'required|string|in:Alta,Baja',
            'Capacidad' => 'required|integer|min:1',
            'Id_Dispositivo' => 'required|exists:Cat_Dispositivo,Id_Dispositivo',
        ]);

        // Actualizamos los datos en la base de datos
        DB::table('Ctrl_Mquinas')
            ->where('Id_Maquina', $request->id_maquina)
            ->update([
                'Txt_Nombre' => $validated['Txt_Nombre'],
                'Txt_Serie_Maquina' => $validated['Txt_Serie_Maquina'],
                'Txt_Tipo_Maquina' => $validated['Txt_Tipo_Maquina'],
                'Txt_Estatus' => $validated['Txt_Estatus'],
                'Capacidad' => $validated['Capacidad'],
                'Id_Dispositivo' => $validated['Id_Dispositivo'],
            ]);

        // Obtenemos el número de serie del dispositivo actualizado para devolverlo a la vista
        $deviceSerie = DB::table('Cat_Dispositivo')
            ->where('Id_Dispositivo', $validated['Id_Dispositivo'])
            ->value('Txt_Serie_Dispositivo');

        return response()->json([
            'success' => true,
            'message' => 'Datos actualizados correctamente.',
            'dispositivoTxt' => $deviceSerie,
        ]);
    } catch (\Exception $e) {
        // Manejo de errores
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error al actualizar los datos: ' . $e->getMessage(),
        ], 500);
    }
}

public function getAvailableDevices($currentDeviceId = null)
{
    // Obtenemos los dispositivos disponibles y el actualmente asignado
    $devices = DB::table('Cat_Dispositivo')
        ->where(function ($query) use ($currentDeviceId) {
            $query->whereNotIn('Id_Dispositivo', function ($subquery) {
                $subquery->select('Id_Dispositivo')
                    ->from('Ctrl_Mquinas')
                    ->whereNotNull('Id_Dispositivo');
            });

            // Incluimos el dispositivo actualmente asignado, si se proporciona
            if ($currentDeviceId) {
                $query->orWhere('Id_Dispositivo', $currentDeviceId);
            }
        })
        ->get(['Id_Dispositivo', 'Txt_Serie_Dispositivo']);

    return response()->json(['devices' => $devices]);
}

public function storeVM(Request $request)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Validación de los datos del formulario
    $validator = Validator::make($request->all(), [
        'Txt_Nombre' => 'required|string|max:255',
        'Id_Planta' => 'required|integer',
        'Txt_Serie_Maquina' => 'required|string|max:255|unique:Ctrl_Mquinas,Txt_Serie_Maquina',
        'Txt_Tipo_Maquina' => 'required|string|max:255',
        'Capacidad' => 'required|integer|min:1',
        'Id_Dispositivo' => 'integer|nullable',
    ]);

    // Si la validación falla, devolver un error
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Los datos proporcionados son incorrectos.',
            'errors' => $validator->errors(),
        ], 400);
    }

    // Obtener el ID del usuario desde la sesión
    $userId = $_SESSION['usuario']->Id_Usuario_Admon;

    // Transacción para asegurar la consistencia de los datos
    DB::beginTransaction();
    try {
        // Insertar la nueva máquina expendedora
        $idMaquina = DB::table('Ctrl_Mquinas')->insertGetId([
            'Txt_Nombre' => $request->Txt_Nombre,
            'Id_Planta' => $request->Id_Planta,
            'Txt_Serie_Maquina' => $request->Txt_Serie_Maquina,
            'Txt_Tipo_Maquina' => $request->Txt_Tipo_Maquina,
            'Txt_Estatus' => 'Alta',
            'Capacidad' => $request->Capacidad,
            'Id_Dispositivo' => $request->Id_Dispositivo,
            'Fecha_Alta' => now(),
            'Fecha_Modificacion' => null,
            'Fecha_Baja' => null,
            'Id_Usuario_Admon_Alta' => $userId,
            'Id_Usuario_Admon_Modificacion' => null,
            'Id_Usuario_Admon_Baja' => null,
        ]);

        // Crear las configuraciones para las charolas de la máquina
        $charolas = [];
        $fechaAlta = now();
        for ($numCharola = 1; $numCharola <= 6; $numCharola++) {
            for ($seleccion = 0; $seleccion <= 9; $seleccion++) {
                $charolas[] = [
                    'Id_Maquina' => $idMaquina,
                    'Num_Charola' => $numCharola,
                    'Seleccion' => (int)("{$numCharola}{$seleccion}"),
                    'Id_Articulo' => null,
                    'Cantidad_Max' => 0,
                    'Cantidad_Min' => 0,
                    'Stock' => 0,
                    'Txt_Estatus' => 'Alta',
                    'Fecha_Alta' => $fechaAlta,
                    'Id_Usuario_Admon_Alta' => $userId,
                ];
            }
        }

        // Insertar las configuraciones en la tabla Configuracion_Maquina
        DB::table('Configuracion_Maquina')->insert($charolas);

        // Confirmar la transacción
        DB::commit();

        // Respuesta de éxito
        return response()->json([
            'success' => true,
            'message' => 'Máquina expendedora y configuraciones de charolas creadas exitosamente.',
        ], 200);

    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Hubo un problema al agregar la máquina y sus configuraciones.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function guardarCambiosPlano(Request $request)
{
    $data = $request->input('updatedData');

    foreach ($data as $item) {
        DB::table('Configuracion_Maquina')
        ->where('Id_Configuracion', $item['idConfiguracion'])
        ->update([
            'Id_Articulo' => $item['idArticulo'] ?: null,
            'Cantidad_Max' => $item['cantidadMax'] ?: 0,
            'Cantidad_Min' => $item['cantidadMin'] ?: 0,
            'Stock' => DB::raw("COALESCE(Stock, 0)") // Si Stock es NULL, lo pone en 0
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Cambios guardados correctamente.']);
}

public function Surtir(Request $request, $lang, $id) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $userId = $_SESSION['usuario']->Id_Usuario_Admon;

    // Obtener el planograma
    $planograma = DB::table('Configuracion_Maquina')
        ->where('Id_Maquina', $id)
        ->leftJoin('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->select(
            'Configuracion_Maquina.Id_Configuracion',
            'Configuracion_Maquina.Id_Articulo',
            'Configuracion_Maquina.Stock',
            'Configuracion_Maquina.Cantidad_Max',
            'Configuracion_Maquina.Cantidad_Min',
            'Configuracion_Maquina.Seleccion',
            'Configuracion_Maquina.Num_Charola',
            'Cat_Articulos.Txt_Codigo',
            'Cat_Articulos.Txt_Descripcion'
        )
        ->get()
        ->groupBy('Num_Charola');

    if ($planograma->isEmpty()) {
        return response()->json(['message' => 'Vending no encontrada'], 404);
    }

    // Si la solicitud incluye un término de búsqueda
    if ($request->has('search')) {
        $search = $request->input('search');
        $articulos = DB::table('Cat_Articulos')
            ->select('Id_Articulo', 'Txt_Descripcion', 'Txt_Codigo')
            ->where('Txt_Descripcion', 'LIKE', "%$search%")
            ->orWhere('Txt_Codigo', 'LIKE', "%$search%")
            ->limit(5) // Limitar a 5 resultados
            ->get();

        return response()->json($articulos);
    }

    // Obtener 4 artículos aleatorios para mostrar inicialmente
    $articulos = DB::table('Cat_Articulos')
        ->select('Id_Articulo', 'Txt_Descripcion', 'Txt_Codigo')
        ->inRandomOrder()
        ->limit(4)
        ->get();

    return view('administracion.vendings.rellenar')->with('planograma', $planograma);
}

public function updateStock(Request $request)
{
    $updatedStock = $request->input('updatedStock');

    foreach ($updatedStock as $stock) {
        DB::table('Configuracion_Maquina')
            ->where('Id_Configuracion', $stock['id'])
            ->update(['Stock' => $stock['stock']]);
    }

    return response()->json(['message' => 'Stock actualizado correctamente']);
}


public function getDispositivos()
{
    $dispositivos = DB::table('Cat_Dispositivo as d')
        ->leftJoin('Ctrl_Mquinas as m', 'd.Id_Dispositivo', '=', 'm.Id_Dispositivo')
        ->leftJoin('Cat_Usuarios_Administradores as uAlta', 'd.Id_Usuario_Admon_Alta', '=', 'uAlta.Id_Usuario_Admon')
        ->leftJoin('Cat_Usuarios_Administradores as uMod', 'd.Id_Usuario_Admon_Modificacion', '=', 'uMod.Id_Usuario_Admon')
        ->select(
            'd.Id_Dispositivo',
            'd.Txt_Serie_Dispositivo',
            'd.Txt_Estatus',
            'm.Id_Maquina',
            'm.Txt_Nombre as Maquina_Nombre',
            DB::raw("CONCAT(uAlta.Txt_Nombre, ' ', uAlta.Txt_ApellidoP, ' ', uAlta.Txt_ApellidoM) as Creado_Por"),
            DB::raw("CONCAT(uMod.Txt_Nombre, ' ', uMod.Txt_ApellidoP, ' ', uMod.Txt_ApellidoM) as Modificado_Por"),
            'd.Fecha_Alta',
            'd.Fecha_Modificacion',
            'd.Fecha_Baja'
        )
        ->get();

    return datatables()->of($dispositivos)
        ->addColumn('Opciones', function ($dispositivo) {
            return '
                <div class="btn-group">
                <button class="btn btn-warning btn-sm edit-btn" data-id="' . $dispositivo->Id_Dispositivo . '"><i class="fa fa-edit"></i> Editar</button>
                <button class="btn btn-danger  btn-sm delete-btn" data-id="' . $dispositivo->Id_Dispositivo . '"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
                    
            ';
        })
        ->rawColumns(['Opciones'])
        ->make(true);
}

        public function showDispositivo($id)
    {
        $dispositivo = DB::table('Cat_Dispositivo')->where('Id_Dispositivo', $id)->first();
        return response()->json($dispositivo);
    }

    public function storeDispositivo(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'Txt_Serie_Dispositivo' => [
                'required',
                'string',
                'min:8',
                'unique:Cat_Dispositivo,Txt_Serie_Dispositivo',
            ],
            'Txt_Estatus' => 'required|in:Alta,Baja',
        ], [
            'Txt_Serie_Dispositivo.required' => 'La serie del dispositivo es obligatoria.',
            'Txt_Serie_Dispositivo.min' => 'La serie del dispositivo debe tener al menos 8 caracteres.',
            'Txt_Serie_Dispositivo.unique' => 'La serie del dispositivo ya existe.',
            'Txt_Estatus.required' => 'El estatus es obligatorio.',
            'Txt_Estatus.in' => 'El estatus debe ser Activo o Inactivo.',
        ]);

        // Obtener el ID del usuario desde la sesión
        $userId = $_SESSION['usuario']->Id_Usuario_Admon;

        // Insertar el nuevo registro
        $id = DB::table('Cat_Dispositivo')->insertGetId([
            'Txt_Serie_Dispositivo' => $request->Txt_Serie_Dispositivo,
            'Txt_Estatus' => $request->Txt_Estatus,
            'Fecha_Alta' => now(),
            'Id_Usuario_Admon_Alta' => $userId,
        ]);

        return response()->json(['success' => 'Dispositivo creado correctamente.', 'id' => $id]);
    }

    public function updateDispositivo(Request $request, $id)
    {
        // Validar los datos recibidos
        $request->validate([
            'Txt_Serie_Dispositivo' => [
                'required',
                'string',
                'min:8',
                "unique:Cat_Dispositivo,Txt_Serie_Dispositivo,{$id},Id_Dispositivo", // Ignora el actual
            ],
            'Txt_Estatus' => 'required|in:Alta,Baja',
        ], [
            'Txt_Serie_Dispositivo.required' => 'La serie del dispositivo es obligatoria.',
            'Txt_Serie_Dispositivo.min' => 'La serie del dispositivo debe tener al menos 8 caracteres.',
            'Txt_Serie_Dispositivo.unique' => 'La serie del dispositivo ya existe.',
            'Txt_Estatus.required' => 'El estatus es obligatorio.',
            'Txt_Estatus.in' => 'El estatus debe ser Activo o Inactivo.',
        ]);

        // Actualizar el dispositivo
        DB::table('Cat_Dispositivo')
            ->where('Id_Dispositivo', $id)
            ->update([
                'Txt_Serie_Dispositivo' => $request->Txt_Serie_Dispositivo,
                'Txt_Estatus' => $request->Txt_Estatus,
                'Fecha_Modificacion' => now(),
                'Id_Usuario_Admon_Modificacion' => $_SESSION['usuario']->Id_Usuario_Admon,
            ]);

        return response()->json(['success' => 'Dispositivo actualizado correctamente.']);
    }

    public function destroyDispositivo($id)
{
    // Verificar si el dispositivo existe antes de eliminarlo
    $dispositivo = DB::table('Cat_Dispositivo')->where('Id_Dispositivo', $id)->first();

    if (!$dispositivo) {
        return response()->json(['error' => 'El dispositivo no existe.'], 404);
    }

    // Eliminar el dispositivo
    DB::table('Cat_Dispositivo')->where('Id_Dispositivo', $id)->delete();

    return response()->json(['success' => 'Dispositivo eliminado correctamente.']);
}



}
