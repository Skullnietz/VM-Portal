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
use App\Exports\ConsumoxEmpleadoExport;



class OperadorController extends Controller
{
    public function Vendings()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['usuario']->Id_Operador;
        // Obtener la cadena de IDs de plantas con acceso
        $plantasAccesoString = $_SESSION['usuario']->PlantasConAcceso;

        // Convertir la cadena en un array de IDs
        $plantasAccesoArray = explode(',', $plantasAccesoString);

        // Crear una cadena con los IDs para la cláusula IN de SQL
        $plantasAccesoIn = "'" . implode("','", $plantasAccesoArray) . "'";

        // Consulta para obtener solo las plantas con acceso
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
        WHERE [Id_Planta] IN ($plantasAccesoIn)
    ");



        return view('operacion.vendings', [
            'plantas' => $plantas,

        ]);
    }

    public function getVendingsData()
    {
        // Comprobamos si la sesión está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Obtenemos el Id_Operador desde la sesión
        $userId = $_SESSION['usuario']->Id_Operador;

        // Obtenemos la cadena de IDs de plantas con acceso para el operador
        $plantasAccesoString = $_SESSION['usuario']->PlantasConAcceso;

        // Convertimos la cadena en un array de IDs
        $plantasAccesoArray = explode(',', $plantasAccesoString);

        // Consulta para obtener los datos de las máquinas que pertenecen a las plantas con acceso y tienen estatus "Alta"
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
            ->whereIn('Ctrl_Mquinas.Id_Planta', $plantasAccesoArray)
            ->where('Ctrl_Mquinas.Txt_Estatus', 'Alta')
            ->get()
            ->groupBy('Txt_Nombre_Planta'); // Agrupamos por el nombre de la planta

        // Devolvemos los datos como JSON para que AJAX los consuma
        return response()->json($vendingsData);
    }

    public function Surtir(Request $request, $lang, $id)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['usuario']->Id_Operador;

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
                'Configuracion_Maquina.Talla',
                'Cat_Articulos.Txt_Codigo',
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Tamano_Espiral',
                'Cat_Articulos.Capacidad_Espiral'
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
            ->select('Id_Articulo', 'Txt_Descripcion', 'Txt_Codigo', 'Tamano_Espiral', 'Capacidad_Espiral')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('operacion.rellenar')->with('planograma', $planograma);
    }


    public function updateStock(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $updatedStock = $request->input('updatedStock');
        $userId = null;
        $userType = 'Operador';
        $resumen = [];

        if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->Id_Operador)) {
            $userId = $_SESSION['usuario']->Id_Operador;
        }

        foreach ($updatedStock as $stock) {
            $config = DB::table('Configuracion_Maquina')
                ->leftJoin('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
                ->select('Configuracion_Maquina.*', 'Cat_Articulos.Txt_Descripcion as Articulo')
                ->where('Configuracion_Maquina.Id_Configuracion', $stock['id'])
                ->first();

            if ($config) {
                $cantidadAnterior = $config->Stock;
                $cantidadNueva = $stock['stock'];
                $cantidadRellenada = $cantidadNueva - $cantidadAnterior;

                if ($cantidadRellenada > 0) {
                    DB::table('Historial_Relleno')->insert([
                        'Id_Configuracion' => $config->Id_Configuracion,
                        'Id_Maquina' => $config->Id_Maquina,
                        'Id_Articulo' => $config->Id_Articulo,
                        'Seleccion' => $config->Seleccion,
                        'Talla' => $config->Talla,
                        'Cantidad_Anterior' => $cantidadAnterior,
                        'Cantidad_Rellenada' => $cantidadRellenada,
                        'Cantidad_Nueva' => $cantidadNueva,
                        'Fecha_Relleno' => now(),
                        'Id_Usuario' => $userId,
                        'Tipo_Usuario' => $userType,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $resumen[] = [
                        'Articulo' => $config->Articulo ?? 'Desconocido',
                        'Cantidad_Anterior' => $cantidadAnterior,
                        'Cantidad_Rellenada' => $cantidadRellenada,
                        'Cantidad_Nueva' => $cantidadNueva,
                    ];
                }

                DB::table('Configuracion_Maquina')
                    ->where('Id_Configuracion', $stock['id'])
                    ->update(['Stock' => $stock['stock']]);
            }
        }

        return response()->json(['message' => 'Stock actualizado correctamente', 'resumen' => $resumen]);
    }

    public function getMissingItems($id)
    {
        $missingItems = DB::table('Configuracion_Maquina')
            ->where('Id_Maquina', $id)
            ->select(DB::raw('SUM(Cantidad_Max - Stock) as total_missing'))
            ->first();

        return response()->json(['missing_count' => $missingItems->total_missing ?? 0]);
    }

    public function downloadMissingItems($id)
    {
        if (ob_get_contents())
            ob_end_clean();
        return Excel::download(new \App\Exports\MissingItemsExport($id), 'faltantes_vending_' . $id . '.xlsx');
    }

    public function indexConsumoEmpleado()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $plantasAccesoString = $_SESSION['usuario']->PlantasConAcceso;
        $plantasAccesoArray = explode(',', $plantasAccesoString);
        $plantasAccesoIn = "'" . implode("','", $plantasAccesoArray) . "'";

        // Fetch plants assigned to the operator
        $plantas = DB::select("
            SELECT [Id_Planta],
                [Txt_Nombre_Planta]
            FROM [Vending_Machine].[dbo].[Cat_Plantas]
            WHERE [Id_Planta] IN ($plantasAccesoIn)
            ORDER BY [Txt_Nombre_Planta] ASC
        ");

        return view('operacion.reportes.consumoxempleado', [
            'plantas' => $plantas
        ]);
    }

    public function getConsumoEmpleadoData(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Validate that the requested plant is in the operator's access list
        $requestedPlantId = $request->input('idPlanta');
        $plantasAccesoString = $_SESSION['usuario']->PlantasConAcceso;
        $plantasAccesoArray = explode(',', $plantasAccesoString);

        if (!in_array($requestedPlantId, $plantasAccesoArray)) {
            return response()->json(['error' => 'Unauthorized access to this plant'], 403);
        }

        $idPlanta = $requestedPlantId;

        // Base query - similar to ReportesClienteController but with censorship
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
                DB::raw("'******' as Nombre"), // CENSORED
                'Cat_Empleados.No_Empleado as Numero_de_empleado',
                'Cat_Area.Txt_Nombre as Area',
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.Talla,'') as Producto"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
                DB::raw("isnull(z.Txt_Codigo_Cliente, Cat_Articulos.Txt_Codigo_Cliente) as Codigo_Cliente"),
                'Ctrl_Consumos.Fecha_Real as Fecha',
                'Ctrl_Consumos.Cantidad'
            )
            ->orderByDesc('Ctrl_Consumos.Fecha_Real');

        // Apply filters
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate . ' 00:00:00';
            $endDate = $request->endDate . ' 23:59:59';
            $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        $totalRecords = $data->count();
        $filteredRecords = $totalRecords; // Simplified for now

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

        $requestedPlantId = $request->input('idPlanta');

        // Security Check: Ensure plant is accessible to operator
        $plantasAccesoString = $_SESSION['usuario']->PlantasConAcceso;
        $plantasAccesoArray = explode(',', $plantasAccesoString);

        if (!$requestedPlantId || !in_array($requestedPlantId, $plantasAccesoArray)) {
            return redirect()->back()->with('error', 'Acceso no autorizado a esta planta.');
        }

        // Trigger export with Censored = true
        return Excel::download(new ConsumoxEmpleadoExport($request, $requestedPlantId, true), 'ConsumoPorEmpleado_Censurado.xlsx');
    }

    public function Profile()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $user = $_SESSION['usuario'];

        $plantasIds = explode(',', $user->PlantasConAcceso ?? '');

        $plantasNombres = DB::table('Cat_Plantas')
            ->whereIn('Id_Planta', $plantasIds)
            ->pluck('Txt_Nombre_Planta')
            ->toArray();

        // Mostrar Plantas con Acceso
        $planta = count($plantasNombres) > 0 ? implode(' | ', $plantasNombres) : 'N/A';

        return view('operacion.profile', compact('user', 'planta'));
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

        $user = $_SESSION['usuario'];

        try {
            DB::table('Cat_Operadores')
                ->where('Id_Operador', $user->Id_Operador)
                ->update([
                    'Txt_Nombre' => $request->nombre,
                    'Txt_ApellidoP' => $request->apellidos,
                    'Txt_Puesto' => $request->puesto
                ]);

            // Update Session
            $_SESSION['usuario']->Txt_Nombre = $request->nombre;
            $_SESSION['usuario']->Txt_ApellidoP = $request->apellidos;
            $_SESSION['usuario']->Txt_Puesto = $request->puesto;

            return redirect()->back()->with('success', 'Perfil actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el perfil.');
        }
    }

    public function updatePassword(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $_SESSION['usuario'];

        try {
            DB::table('Cat_Operadores')
                ->where('Id_Operador', $user->Id_Operador)
                ->update([
                    'Contrasenia' => $request->password
                ]);

            // Update Session
            $_SESSION['usuario']->Contrasenia = $request->password;

            return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la contraseña.');
        }
    }
}
