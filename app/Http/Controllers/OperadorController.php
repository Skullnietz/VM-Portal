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
}
