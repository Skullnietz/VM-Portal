<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConsumoxEmpleadoMultiExport implements WithMultipleSheets
{
    protected $request;
    protected $idPlanta;

    public function __construct($request, $idPlanta)
    {
        $this->request = $request;
        $this->idPlanta = $idPlanta;
    }

    public function sheets(): array
    {
        return [
            'Detalle de consumos' => new ConsumoxEmpleadoExport($this->request, $this->idPlanta),
            'Resumen por cliente' => new ResumenClienteExport($this->request, $this->idPlanta),
        ];
    }
}
