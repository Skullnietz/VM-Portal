<?php

namespace App\Services;

use App\Services\Reporte\ReportePlantInterface;
use App\Services\Reporte\ReporteGeneralPlant;
use Carbon\Carbon;

class ReporteConsumoService
{
    /**
     * Registry of available report implementations keyed by Plantilla slug.
     * To add a per-plant report: register its class here and set Plantilla on
     * the Configuracion_Reportes row.
     *
     * Example:
     *   'reporte_toyota' => \App\Services\Reporte\ReporteToyotaPlant::class,
     */
    private $registry = [
        'consumo_general' => ReporteGeneralPlant::class,
    ];

    public function resolver($plantilla)
    {
        $key = $plantilla ?: 'consumo_general';

        if (isset($this->registry[$key])) {
            return app($this->registry[$key]);
        }

        return app(ReporteGeneralPlant::class);
    }

    public function tiposDisponibles()
    {
        $result = [];
        foreach ($this->registry as $slug => $class) {
            $result[$slug] = app($class)->getNombre();
        }
        return $result;
    }
}
