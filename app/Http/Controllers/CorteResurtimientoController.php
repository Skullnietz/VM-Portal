<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CortePreExport;
use App\Exports\CortePostExport;
use App\Exports\ConsumoEntreResurtimientosExport;
use App\Exports\DiscrepanciasExport;

class CorteResurtimientoController extends Controller
{
    /**
     * Detecta el tipo de usuario desde la sesión y retorna info de contexto.
     */
    private function getUserContext()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario']->Id_Usuario_Admon)) {
            return [
                'type' => 'Admin',
                'id' => $_SESSION['usuario']->Id_Usuario_Admon,
                'plantas' => null, // admin ve todo
            ];
        } elseif (isset($_SESSION['usuario']->Id_Operador)) {
            $plantasAccesoArray = explode(',', $_SESSION['usuario']->PlantasConAcceso);
            return [
                'type' => 'Operador',
                'id' => $_SESSION['usuario']->Id_Operador,
                'plantas' => $plantasAccesoArray,
            ];
        }

        return ['type' => null, 'id' => null, 'plantas' => null];
    }

    /**
     * Aplica filtro de plantas si el usuario es operador.
     */
    private function applyPlantFilter($query, $ctx, $plantColumn = 'Ctrl_Mquinas.Id_Planta')
    {
        if ($ctx['type'] === 'Operador' && $ctx['plantas']) {
            $query->whereIn($plantColumn, $ctx['plantas']);
        }
        return $query;
    }

    /**
     * Obtener plantas accesibles para el usuario.
     */
    private function getPlantasForUser($ctx)
    {
        $query = DB::table('Cat_Plantas')->select('Id_Planta', 'Txt_Nombre_Planta as Nombre');
        if ($ctx['type'] === 'Operador' && $ctx['plantas']) {
            $query->whereIn('Id_Planta', $ctx['plantas']);
        }
        return $query->orderBy('Txt_Nombre_Planta')->get();
    }

    // ========================== FASE 1: CORTE PRE ==========================

    /**
     * Genera un corte PRE-resurtimiento para una máquina.
     */
    public function generarCortePre($lang, $idMaquina)
    {
        $ctx = $this->getUserContext();

        // Verificar acceso a la máquina
        $maquina = DB::table('Ctrl_Mquinas')
            ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select('Ctrl_Mquinas.Id_Maquina', 'Ctrl_Mquinas.Txt_Nombre', 'Cat_Plantas.Txt_Nombre_Planta', 'Ctrl_Mquinas.Id_Planta')
            ->where('Ctrl_Mquinas.Id_Maquina', $idMaquina);

        $maquina = $this->applyPlantFilter($maquina, $ctx)->first();

        if (!$maquina) {
            return response()->json(['error' => 'Máquina no encontrada o sin acceso'], 404);
        }

        // Obtener slots con artículos
        $slots = DB::table('Configuracion_Maquina')
            ->leftJoin('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Configuracion_Maquina.Id_Configuracion',
                'Configuracion_Maquina.Id_Articulo',
                'Configuracion_Maquina.Seleccion',
                'Configuracion_Maquina.Num_Charola',
                'Configuracion_Maquina.Talla',
                'Configuracion_Maquina.Stock',
                'Configuracion_Maquina.Cantidad_Max',
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo'
            )
            ->where('Configuracion_Maquina.Id_Maquina', $idMaquina)
            ->whereNotNull('Configuracion_Maquina.Id_Articulo')
            ->orderBy('Configuracion_Maquina.Num_Charola')
            ->orderBy('Configuracion_Maquina.Seleccion')
            ->get();

        // Crear registro de corte PRE
        $idCorte = DB::table('Cortes_Resurtimiento')->insertGetId([
            'Id_Maquina' => $idMaquina,
            'Tipo_Corte' => 'PRE',
            'Fecha_Corte' => now(),
            'Id_Usuario' => $ctx['id'],
            'Tipo_Usuario' => $ctx['type'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar detalle del corte
        foreach ($slots as $slot) {
            $cantidadNecesaria = max(0, $slot->Cantidad_Max - $slot->Stock);
            DB::table('Corte_Detalle')->insert([
                'Id_Corte' => $idCorte,
                'Id_Configuracion' => $slot->Id_Configuracion,
                'Id_Articulo' => $slot->Id_Articulo,
                'Seleccion' => $slot->Seleccion,
                'Talla' => $slot->Talla,
                'Stock_Actual' => $slot->Stock,
                'Cantidad_Max' => $slot->Cantidad_Max,
                'Cantidad_Necesaria' => $cantidadNecesaria,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect("/{$lang}/corte/pre/{$idCorte}/ver");
    }

    /**
     * Ver un corte PRE existente.
     */
    public function verCortePre($lang, $idCorte)
    {
        $ctx = $this->getUserContext();

        $corte = DB::table('Cortes_Resurtimiento')
            ->join('Ctrl_Mquinas', 'Cortes_Resurtimiento.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select(
                'Cortes_Resurtimiento.*',
                'Ctrl_Mquinas.Txt_Nombre as Maquina',
                'Cat_Plantas.Txt_Nombre_Planta as Planta'
            )
            ->where('Cortes_Resurtimiento.Id_Corte', $idCorte)
            ->where('Cortes_Resurtimiento.Tipo_Corte', 'PRE');

        $corte = $this->applyPlantFilter($corte, $ctx)->first();

        if (!$corte) {
            return redirect()->back()->with('error', 'Corte no encontrado');
        }

        $detalles = DB::table('Corte_Detalle')
            ->leftJoin('Cat_Articulos', 'Corte_Detalle.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Corte_Detalle.*',
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo'
            )
            ->where('Corte_Detalle.Id_Corte', $idCorte)
            ->orderBy('Corte_Detalle.Seleccion')
            ->get()
            ->groupBy('Seleccion');

        // Resumen agregado por artículo+talla
        $resumen = DB::table('Corte_Detalle')
            ->leftJoin('Cat_Articulos', 'Corte_Detalle.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo',
                'Corte_Detalle.Talla',
                DB::raw('SUM(Corte_Detalle.Cantidad_Necesaria) as Total_Necesario')
            )
            ->where('Corte_Detalle.Id_Corte', $idCorte)
            ->where('Corte_Detalle.Cantidad_Necesaria', '>', 0)
            ->groupBy('Cat_Articulos.Txt_Descripcion', 'Cat_Articulos.Txt_Codigo', 'Corte_Detalle.Talla')
            ->get();

        return view('cortes.pre', compact('corte', 'detalles', 'resumen', 'ctx'));
    }

    /**
     * Exportar corte PRE a Excel.
     */
    public function exportCortePre($idCorte)
    {
        $ctx = $this->getUserContext();
        $corte = DB::table('Cortes_Resurtimiento')
            ->join('Ctrl_Mquinas', 'Cortes_Resurtimiento.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->where('Cortes_Resurtimiento.Id_Corte', $idCorte);
        $corte = $this->applyPlantFilter($corte, $ctx)->first();

        if (!$corte) {
            return redirect()->back()->with('error', 'Corte no encontrado');
        }

        if (ob_get_contents()) ob_end_clean();
        return Excel::download(new CortePreExport($idCorte), 'corte_pre_' . $idCorte . '.xlsx');
    }

    // ========================== FASE 2: CORTE POST ==========================

    /**
     * Genera un corte POST después de que el resurtimiento fue guardado.
     */
    public function generarCortePost(Request $request)
    {
        $ctx = $this->getUserContext();
        $idCortePre = $request->input('id_corte_pre');

        // Verificar que el corte PRE existe
        $cortePre = DB::table('Cortes_Resurtimiento')
            ->where('Id_Corte', $idCortePre)
            ->where('Tipo_Corte', 'PRE')
            ->first();

        if (!$cortePre) {
            return response()->json(['error' => 'Corte PRE no encontrado'], 404);
        }

        $idMaquina = $cortePre->Id_Maquina;

        // Obtener slots actuales (después del resurtido)
        $slots = DB::table('Configuracion_Maquina')
            ->leftJoin('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Configuracion_Maquina.Id_Configuracion',
                'Configuracion_Maquina.Id_Articulo',
                'Configuracion_Maquina.Seleccion',
                'Configuracion_Maquina.Talla',
                'Configuracion_Maquina.Stock',
                'Configuracion_Maquina.Cantidad_Max'
            )
            ->where('Configuracion_Maquina.Id_Maquina', $idMaquina)
            ->whereNotNull('Configuracion_Maquina.Id_Articulo')
            ->get()
            ->keyBy('Id_Configuracion');

        // Obtener detalles del corte PRE para comparar
        $detallesPre = DB::table('Corte_Detalle')
            ->where('Id_Corte', $idCortePre)
            ->get()
            ->keyBy('Id_Configuracion');

        // Crear corte POST
        $idCortePost = DB::table('Cortes_Resurtimiento')->insertGetId([
            'Id_Maquina' => $idMaquina,
            'Tipo_Corte' => 'POST',
            'Fecha_Corte' => now(),
            'Id_Usuario' => $ctx['id'],
            'Tipo_Usuario' => $ctx['type'],
            'Id_Corte_Pre' => $idCortePre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar detalles POST con comparación
        foreach ($detallesPre as $idConfig => $detPre) {
            $slotActual = $slots->get($idConfig);
            $stockPost = $slotActual ? $slotActual->Stock : $detPre->Stock_Actual;
            $cantidadRellenada = $stockPost - $detPre->Stock_Actual;

            DB::table('Corte_Detalle')->insert([
                'Id_Corte' => $idCortePost,
                'Id_Configuracion' => $idConfig,
                'Id_Articulo' => $detPre->Id_Articulo,
                'Seleccion' => $detPre->Seleccion,
                'Talla' => $detPre->Talla,
                'Stock_Actual' => $detPre->Stock_Actual, // Stock al momento del PRE
                'Cantidad_Max' => $detPre->Cantidad_Max,
                'Cantidad_Necesaria' => $detPre->Cantidad_Necesaria, // Lo que se planeaba
                'Stock_Post' => $stockPost, // Stock actual después del resurtido
                'Cantidad_Rellenada' => max(0, $cantidadRellenada),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Corte POST generado correctamente',
            'id_corte_post' => $idCortePost,
        ]);
    }

    /**
     * Ver un corte POST con comparación PRE vs POST.
     */
    public function verCortePost($lang, $idCorte)
    {
        $ctx = $this->getUserContext();

        $corte = DB::table('Cortes_Resurtimiento')
            ->join('Ctrl_Mquinas', 'Cortes_Resurtimiento.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select(
                'Cortes_Resurtimiento.*',
                'Ctrl_Mquinas.Txt_Nombre as Maquina',
                'Cat_Plantas.Txt_Nombre_Planta as Planta'
            )
            ->where('Cortes_Resurtimiento.Id_Corte', $idCorte)
            ->where('Cortes_Resurtimiento.Tipo_Corte', 'POST');

        $corte = $this->applyPlantFilter($corte, $ctx)->first();

        if (!$corte) {
            return redirect()->back()->with('error', 'Corte no encontrado');
        }

        $detalles = DB::table('Corte_Detalle')
            ->leftJoin('Cat_Articulos', 'Corte_Detalle.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Corte_Detalle.*',
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo'
            )
            ->where('Corte_Detalle.Id_Corte', $idCorte)
            ->orderBy('Corte_Detalle.Seleccion')
            ->get();

        // Resumen agregado
        $resumen = DB::table('Corte_Detalle')
            ->leftJoin('Cat_Articulos', 'Corte_Detalle.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo',
                'Corte_Detalle.Talla',
                DB::raw('SUM(Corte_Detalle.Cantidad_Necesaria) as Total_Planeado'),
                DB::raw('SUM(Corte_Detalle.Cantidad_Rellenada) as Total_Rellenado')
            )
            ->where('Corte_Detalle.Id_Corte', $idCorte)
            ->groupBy('Cat_Articulos.Txt_Descripcion', 'Cat_Articulos.Txt_Codigo', 'Corte_Detalle.Talla')
            ->get();

        return view('cortes.post', compact('corte', 'detalles', 'resumen', 'ctx'));
    }

    /**
     * Historial de cortes con filtros.
     */
    public function historialCortes($lang)
    {
        $ctx = $this->getUserContext();
        $plantas = $this->getPlantasForUser($ctx);

        return view('cortes.historial', compact('plantas', 'ctx'));
    }

    /**
     * Datos AJAX para DataTable del historial de cortes.
     */
    public function getHistorialCortesData(Request $request)
    {
        $ctx = $this->getUserContext();

        $query = DB::table('Cortes_Resurtimiento')
            ->join('Ctrl_Mquinas', 'Cortes_Resurtimiento.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->join('Cat_Plantas', 'Ctrl_Mquinas.Id_Planta', '=', 'Cat_Plantas.Id_Planta')
            ->select(
                'Cortes_Resurtimiento.Id_Corte',
                'Cortes_Resurtimiento.Tipo_Corte',
                'Cortes_Resurtimiento.Fecha_Corte',
                'Cortes_Resurtimiento.Tipo_Usuario',
                'Ctrl_Mquinas.Txt_Nombre as Maquina',
                'Cat_Plantas.Txt_Nombre_Planta as Planta'
            )
            ->orderByDesc('Cortes_Resurtimiento.Fecha_Corte');

        $this->applyPlantFilter($query, $ctx);

        if ($request->filled('planta_id')) {
            $query->where('Ctrl_Mquinas.Id_Planta', $request->planta_id);
        }

        if ($request->filled('tipo_corte')) {
            $query->where('Cortes_Resurtimiento.Tipo_Corte', $request->tipo_corte);
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('Cortes_Resurtimiento.Fecha_Corte', [
                $request->startDate . ' 00:00:00',
                $request->endDate . ' 23:59:59'
            ]);
        }

        $totalRecords = $query->count();

        $data = $query->offset($request->start ?? 0)
            ->limit($request->length ?? 100)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    // ========================== FASE 3: ANALÍTICA ==========================

    /**
     * Vista de consumo entre resurtimientos.
     */
    public function consumoEntreResurtimientos($lang)
    {
        $ctx = $this->getUserContext();
        $plantas = $this->getPlantasForUser($ctx);

        return view('cortes.consumo-entre-resurtimientos', compact('plantas', 'ctx'));
    }

    /**
     * Datos AJAX: consumo entre dos fechas de resurtimiento para una máquina.
     */
    public function getConsumoEntreResurtimientosData(Request $request)
    {
        $ctx = $this->getUserContext();
        $idMaquina = $request->input('id_maquina');

        if (!$idMaquina) {
            return response()->json(['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }

        // Verificar acceso
        $maquina = DB::table('Ctrl_Mquinas')->where('Id_Maquina', $idMaquina);
        $maquina = $this->applyPlantFilter($maquina, $ctx, 'Id_Planta')->first();
        if (!$maquina) {
            return response()->json(['error' => 'Sin acceso'], 403);
        }

        // Obtener fechas de resurtimiento (cortes POST o Historial_Relleno)
        $resurtimientos = DB::table('Historial_Relleno')
            ->where('Id_Maquina', $idMaquina)
            ->select(DB::raw('DISTINCT CAST(Fecha_Relleno AS DATE) as Fecha'))
            ->orderBy('Fecha')
            ->pluck('Fecha')
            ->toArray();

        if (count($resurtimientos) < 1) {
            return response()->json(['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }

        $result = [];
        // Agregar una fecha "inicio" antes del primer resurtimiento
        array_unshift($resurtimientos, null);

        for ($i = 1; $i < count($resurtimientos); $i++) {
            $fechaInicio = $resurtimientos[$i - 1] ? $resurtimientos[$i - 1] . ' 00:00:00' : '2000-01-01 00:00:00';
            $fechaFin = $resurtimientos[$i] . ' 23:59:59';

            $consumos = DB::table('Ctrl_Consumos')
                ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
                ->where('Ctrl_Consumos.Id_Maquina', $idMaquina)
                ->whereBetween('Ctrl_Consumos.Fecha_Real', [$fechaInicio, $fechaFin])
                ->select(
                    DB::raw("'" . $resurtimientos[$i] . "' as Fecha_Resurtimiento"),
                    'Cat_Articulos.Txt_Descripcion as Articulo',
                    DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total_Consumido')
                )
                ->groupBy('Cat_Articulos.Txt_Descripcion')
                ->get();

            foreach ($consumos as $c) {
                $result[] = $c;
            }
        }

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result),
            'data' => $result,
        ]);
    }

    /**
     * Vista de discrepancias de inventario.
     */
    public function discrepancias($lang)
    {
        $ctx = $this->getUserContext();
        $plantas = $this->getPlantasForUser($ctx);

        return view('cortes.discrepancias', compact('plantas', 'ctx'));
    }

    /**
     * Datos AJAX: discrepancias de inventario para una máquina.
     */
    public function getDiscrepanciasData(Request $request)
    {
        $ctx = $this->getUserContext();
        $idMaquina = $request->input('id_maquina');

        if (!$idMaquina) {
            return response()->json(['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }

        // Verificar acceso
        $maquina = DB::table('Ctrl_Mquinas')->where('Id_Maquina', $idMaquina);
        $maquina = $this->applyPlantFilter($maquina, $ctx, 'Id_Planta')->first();
        if (!$maquina) {
            return response()->json(['error' => 'Sin acceso'], 403);
        }

        // Query con OUTER APPLY para SQL Server
        $data = DB::select("
            SELECT
                cm.Seleccion,
                cm.Num_Charola AS Charola,
                ca.Txt_Descripcion AS Articulo,
                cm.Talla,
                ISNULL(hr.Cantidad_Nueva, cm.Stock) AS Stock_Ultimo_Relleno,
                hr.Fecha_Relleno AS Fecha_Ultimo_Relleno,
                ISNULL(consumos.Total_Consumido, 0) AS Consumos_Registrados,
                (ISNULL(hr.Cantidad_Nueva, cm.Stock) - ISNULL(consumos.Total_Consumido, 0)) AS Stock_Teorico,
                cm.Stock AS Stock_Actual,
                (ISNULL(hr.Cantidad_Nueva, cm.Stock) - ISNULL(consumos.Total_Consumido, 0) - cm.Stock) AS Discrepancia
            FROM Configuracion_Maquina cm
            INNER JOIN Cat_Articulos ca ON cm.Id_Articulo = ca.Id_Articulo
            OUTER APPLY (
                SELECT TOP 1
                    h.Cantidad_Nueva, h.Fecha_Relleno
                FROM Historial_Relleno h
                WHERE h.Id_Configuracion = cm.Id_Configuracion
                ORDER BY h.Fecha_Relleno DESC
            ) hr
            OUTER APPLY (
                SELECT
                    SUM(c.Cantidad) AS Total_Consumido
                FROM Ctrl_Consumos c
                WHERE c.Id_Maquina = cm.Id_Maquina
                  AND c.Seleccion = cm.Seleccion
                  AND c.Fecha_Real >= ISNULL(hr.Fecha_Relleno, '2000-01-01')
            ) consumos
            WHERE cm.Id_Maquina = ?
              AND cm.Id_Articulo IS NOT NULL
            ORDER BY cm.Num_Charola, cm.Seleccion
        ", [$idMaquina]);

        return response()->json([
            'draw' => intval($request->draw ?? 1),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    /**
     * Exportar discrepancias a Excel.
     */
    public function exportDiscrepancias(Request $request)
    {
        $idMaquina = $request->input('id_maquina');
        if (ob_get_contents()) ob_end_clean();
        return Excel::download(new DiscrepanciasExport($idMaquina), 'discrepancias_vm_' . $idMaquina . '.xlsx');
    }

    // ========================== FASE 4: TENDENCIAS + DASHBOARD ==========================

    /**
     * Vista de tendencias de consumo.
     */
    public function tendencias($lang)
    {
        $ctx = $this->getUserContext();
        $plantas = $this->getPlantasForUser($ctx);

        return view('cortes.tendencias', compact('plantas', 'ctx'));
    }

    /**
     * Datos JSON para gráficas de tendencias.
     */
    public function getTendenciasData(Request $request)
    {
        $ctx = $this->getUserContext();
        $idPlanta = $request->input('id_planta');
        $idMaquina = $request->input('id_maquina');
        $meses = $request->input('meses', 6);

        $query = DB::table('Ctrl_Consumos')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->select(
                DB::raw('YEAR(Ctrl_Consumos.Fecha_Real) as Anio'),
                DB::raw('MONTH(Ctrl_Consumos.Fecha_Real) as Mes'),
                'Cat_Articulos.Txt_Descripcion as Articulo',
                DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total_Consumido')
            )
            ->where('Ctrl_Consumos.Fecha_Real', '>=', DB::raw("DATEADD(MONTH, -{$meses}, GETDATE())"));

        $this->applyPlantFilter($query, $ctx);

        if ($idPlanta) {
            $query->where('Ctrl_Mquinas.Id_Planta', $idPlanta);
        }
        if ($idMaquina) {
            $query->where('Ctrl_Consumos.Id_Maquina', $idMaquina);
        }

        $data = $query->groupBy(
                DB::raw('YEAR(Ctrl_Consumos.Fecha_Real)'),
                DB::raw('MONTH(Ctrl_Consumos.Fecha_Real)'),
                'Cat_Articulos.Txt_Descripcion'
            )
            ->orderBy('Anio')
            ->orderBy('Mes')
            ->get();

        // Estructurar para Chart.js
        $labels = [];
        $datasets = [];
        $articleColors = [];

        foreach ($data as $row) {
            $label = $row->Anio . '-' . str_pad($row->Mes, 2, '0', STR_PAD_LEFT);
            if (!in_array($label, $labels)) {
                $labels[] = $label;
            }
            if (!isset($datasets[$row->Articulo])) {
                $datasets[$row->Articulo] = [];
            }
            $datasets[$row->Articulo][$label] = $row->Total_Consumido;
        }

        // Formatear datasets para Chart.js
        $chartDatasets = [];
        $colorIndex = 0;
        $colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'];

        foreach ($datasets as $articulo => $values) {
            $dataPoints = [];
            foreach ($labels as $label) {
                $dataPoints[] = $values[$label] ?? 0;
            }
            $chartDatasets[] = [
                'label' => $articulo,
                'data' => $dataPoints,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)] . '33',
                'fill' => false,
            ];
            $colorIndex++;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $chartDatasets,
        ]);
    }

    /**
     * Dashboard operativo.
     */
    public function dashboard($lang)
    {
        $ctx = $this->getUserContext();
        $plantas = $this->getPlantasForUser($ctx);

        return view('cortes.dashboard', compact('plantas', 'ctx'));
    }

    /**
     * Datos JSON para el dashboard operativo.
     */
    public function getDashboardData(Request $request)
    {
        $ctx = $this->getUserContext();
        $idPlanta = $request->input('id_planta');

        // 1. Máquinas con stock bajo
        $queryBajoStock = DB::table('Configuracion_Maquina')
            ->join('Ctrl_Mquinas', 'Configuracion_Maquina.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->whereNotNull('Configuracion_Maquina.Id_Articulo')
            ->whereRaw('Configuracion_Maquina.Stock <= Configuracion_Maquina.Cantidad_Min');

        $this->applyPlantFilter($queryBajoStock, $ctx);
        if ($idPlanta) $queryBajoStock->where('Ctrl_Mquinas.Id_Planta', $idPlanta);

        $maquinasBajoStock = $queryBajoStock
            ->select(DB::raw('COUNT(DISTINCT Configuracion_Maquina.Id_Maquina) as total'))
            ->first()->total;

        // 2. Top 5 artículos más consumidos (últimos 30 días)
        $queryTop = DB::table('Ctrl_Consumos')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->where('Ctrl_Consumos.Fecha_Real', '>=', DB::raw("DATEADD(DAY, -30, GETDATE())"));

        $this->applyPlantFilter($queryTop, $ctx);
        if ($idPlanta) $queryTop->where('Ctrl_Mquinas.Id_Planta', $idPlanta);

        $topArticulos = $queryTop
            ->select('Cat_Articulos.Txt_Descripcion', DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total'))
            ->groupBy('Cat_Articulos.Txt_Descripcion')
            ->orderByDesc('Total')
            ->limit(5)
            ->get();

        // 3. Total consumido últimos 30 días
        $queryTotalConsumo = DB::table('Ctrl_Consumos')
            ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->where('Ctrl_Consumos.Fecha_Real', '>=', DB::raw("DATEADD(DAY, -30, GETDATE())"));

        $this->applyPlantFilter($queryTotalConsumo, $ctx);
        if ($idPlanta) $queryTotalConsumo->where('Ctrl_Mquinas.Id_Planta', $idPlanta);

        $totalConsumo = $queryTotalConsumo->sum('Ctrl_Consumos.Cantidad');

        // 4. Total resurtido últimos 30 días
        $queryTotalResurtido = DB::table('Historial_Relleno')
            ->join('Ctrl_Mquinas', 'Historial_Relleno.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->where('Historial_Relleno.Fecha_Relleno', '>=', DB::raw("DATEADD(DAY, -30, GETDATE())"));

        $this->applyPlantFilter($queryTotalResurtido, $ctx);
        if ($idPlanta) $queryTotalResurtido->where('Ctrl_Mquinas.Id_Planta', $idPlanta);

        $totalResurtido = $queryTotalResurtido->sum('Historial_Relleno.Cantidad_Rellenada');

        // 5. Alertas de stock bajo (slots críticos)
        $queryAlertas = DB::table('Configuracion_Maquina')
            ->join('Ctrl_Mquinas', 'Configuracion_Maquina.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->join('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->whereRaw('Configuracion_Maquina.Stock <= Configuracion_Maquina.Cantidad_Min')
            ->whereNotNull('Configuracion_Maquina.Id_Articulo');

        $this->applyPlantFilter($queryAlertas, $ctx);
        if ($idPlanta) $queryAlertas->where('Ctrl_Mquinas.Id_Planta', $idPlanta);

        $alertas = $queryAlertas
            ->select(
                'Ctrl_Mquinas.Txt_Nombre as Maquina',
                'Cat_Articulos.Txt_Descripcion as Articulo',
                'Configuracion_Maquina.Seleccion',
                'Configuracion_Maquina.Stock',
                'Configuracion_Maquina.Cantidad_Min',
                'Configuracion_Maquina.Cantidad_Max'
            )
            ->orderBy('Configuracion_Maquina.Stock')
            ->limit(20)
            ->get();

        // 6. Consumo diario últimos 7 días (para mini chart)
        $queryDiario = DB::table('Ctrl_Consumos')
            ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->where('Ctrl_Consumos.Fecha_Real', '>=', DB::raw("DATEADD(DAY, -7, GETDATE())"));

        $this->applyPlantFilter($queryDiario, $ctx);
        if ($idPlanta) $queryDiario->where('Ctrl_Mquinas.Id_Planta', $idPlanta);

        $consumoDiario = $queryDiario
            ->select(
                DB::raw('CAST(Ctrl_Consumos.Fecha_Real AS DATE) as Fecha'),
                DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total')
            )
            ->groupBy(DB::raw('CAST(Ctrl_Consumos.Fecha_Real AS DATE)'))
            ->orderBy('Fecha')
            ->get();

        return response()->json([
            'maquinas_bajo_stock' => $maquinasBajoStock,
            'top_articulos' => $topArticulos,
            'total_consumo_30d' => $totalConsumo,
            'total_resurtido_30d' => $totalResurtido,
            'alertas' => $alertas,
            'consumo_diario' => $consumoDiario,
        ]);
    }

    /**
     * Obtener máquinas de una planta (para selectores AJAX).
     */
    public function getMaquinasByPlanta(Request $request)
    {
        $ctx = $this->getUserContext();
        $idPlanta = $request->input('id_planta');

        if ($ctx['type'] === 'Operador' && $ctx['plantas'] && !in_array($idPlanta, $ctx['plantas'])) {
            return response()->json([], 403);
        }

        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $idPlanta)
            ->where('Txt_Estatus', 'Alta')
            ->select('Id_Maquina', 'Txt_Nombre')
            ->orderBy('Txt_Nombre')
            ->get();

        return response()->json($maquinas);
    }
}
