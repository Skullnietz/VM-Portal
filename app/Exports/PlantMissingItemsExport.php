<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PlantMissingItemsExport implements WithMultipleSheets
{
    protected $idPlanta;
    protected $nombrePlanta;

    public function __construct($idPlanta)
    {
        $this->idPlanta = $idPlanta;
        $this->nombrePlanta = DB::table('Cat_Plantas')
            ->where('Id_Planta', $idPlanta)
            ->value('Txt_Nombre_Planta') ?? 'Planta';
    }

    public function sheets(): array
    {
        return [
            new PlantMissingGeneralSheet($this->idPlanta, $this->nombrePlanta),
            new PlantMissingByVendingSheet($this->idPlanta, $this->nombrePlanta),
            new PlantMissingDetailSheet($this->idPlanta, $this->nombrePlanta),
        ];
    }
}
