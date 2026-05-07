<?php

namespace App\Services\Reporte;

use App\Exports\ConsumoxEmpleadoMultiExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteGeneralPlant implements ReportePlantInterface
{
    public function generar(Carbon $fechaInicio, Carbon $fechaFin, int $idPlanta, string $frecuencia): string
    {
        $fakeRequest = new Request([
            'area'      => [],
            'product'   => [],
            'employee'  => [],
            'dateRange' => $fechaInicio->format('Y-m-d') . ' - ' . $fechaFin->format('Y-m-d'),
        ]);

        $filename = 'reporte_consumos_' . $frecuencia . '_' . date('Ymd_His') . '.xlsx';
        Excel::store(new ConsumoxEmpleadoMultiExport($fakeRequest, $idPlanta), $filename, 'local');

        return $filename;
    }

    public function getNombre(): string
    {
        return 'Reporte General (estilo Audi Puebla)';
    }
}
