<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EmpleadosMultiSheetExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Empleados' => new EmpleadosExport(),
            'Resumen por cliente' => new ClientesResumenExport(),
        ];
    }
}
