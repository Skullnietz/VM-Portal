<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\ConsumoxEmpleadoDetailSheet;
use App\Exports\ConsumoxEmpleadoSummarySheet;

class ConsumoxEmpleadoExport implements WithMultipleSheets
{
    protected $request;
    protected $idPlanta;
    protected $censored;

    public function __construct($request, $idPlanta, $censored = false)
    {
        $this->request = $request;
        $this->idPlanta = $idPlanta;
        $this->censored = $censored;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Summary (Resumen)
        $sheets[] = new ConsumoxEmpleadoSummarySheet($this->request, $this->idPlanta);

        // Sheet 2: Detail (Detalle)
        $sheets[] = new ConsumoxEmpleadoDetailSheet($this->request, $this->idPlanta, $this->censored);

        return $sheets;
    }
}