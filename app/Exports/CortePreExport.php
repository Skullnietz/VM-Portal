<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CortePreExport implements WithMultipleSheets
{
    protected $idCorte;
    protected $nombreMaquina;

    public function __construct($idCorte)
    {
        $this->idCorte = $idCorte;

        $corte = DB::table('Cortes_Resurtimiento')
            ->join('Ctrl_Mquinas', 'Cortes_Resurtimiento.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina')
            ->where('Cortes_Resurtimiento.Id_Corte', $idCorte)
            ->first();

        $this->nombreMaquina = $corte->Txt_Nombre ?? 'Máquina';
    }

    public function sheets(): array
    {
        return [
            new CortePreDetailSheet($this->idCorte, $this->nombreMaquina),
            new CortePreSummarySheet($this->idCorte, $this->nombreMaquina),
        ];
    }
}
