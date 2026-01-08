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
            new ConsumoxEmpleadoSummarySheet($this->request, $this->idPlanta),
            new ConsumoxEmpleadoDetailSheet($this->request, $this->idPlanta),
            new ResumenClienteExport($this->request, $this->idPlanta),
        ];
    }
}
