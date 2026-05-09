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
    protected $vendingId;

    public function __construct($request, $idPlanta, $censored = false, $vendingId = null)
    {
        $this->request = $request;
        $this->idPlanta = $idPlanta;
        $this->censored = $censored;
        $this->vendingId = $vendingId;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Summary (Resumen)
        $sheets[] = new ConsumoxEmpleadoSummarySheet($this->request, $this->idPlanta);

        // Sheet 2: Detail (Detalle)
        $sheets[] = new ConsumoxEmpleadoDetailSheet($this->request, $this->idPlanta, $this->censored, null, $this->vendingId);

        return $sheets;
    }
}