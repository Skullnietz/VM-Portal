<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MissingItemsExport implements WithMultipleSheets
{
    protected $idMaquina;
    protected $nombreMaquina;

    public function __construct($idMaquina)
    {
        $this->idMaquina = $idMaquina;
        // Obtener el nombre de la máquina una sola vez
        $this->nombreMaquina = DB::table('Ctrl_Mquinas')
            ->where('Id_Maquina', $idMaquina)
            ->value('Txt_Nombre');
    }

    public function sheets(): array
    {
        return [
            new MissingItemsDetailSheet($this->idMaquina, $this->nombreMaquina),
            new MissingItemsSummarySheet($this->idMaquina, $this->nombreMaquina),
        ];
    }
}
