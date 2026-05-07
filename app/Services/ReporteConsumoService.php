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
    private array $registry = [
        'consumo_general' => ReporteGeneralPlant::class,
    ];

    public function resolver(?string $plantilla): ReportePlantInterface
    {
        $key = $plantilla ?? 'consumo_general';

        if (isset($this->registry[$key])) {
            return app($this->registry[$key]);
        }

        return app(ReporteGeneralPlant::class);
    }

    /**
     * Returns all registered report types as [slug => name] for UI selectors.
     */
    public function tiposDisponibles(): array
    {
        return collect($this->registry)
            ->map(fn($class) => app($class)->getNombre())
            ->all();
    }
}
