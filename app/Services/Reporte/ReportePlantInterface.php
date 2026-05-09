<?php

namespace App\Services\Reporte;

use Carbon\Carbon;

interface ReportePlantInterface
{
    /**
     * Generates the Excel file for the report and returns the stored filename.
     */
    public function generar(Carbon $fechaInicio, Carbon $fechaFin, int $idPlanta, string $frecuencia): string;

    public function getNombre(): string;
}
