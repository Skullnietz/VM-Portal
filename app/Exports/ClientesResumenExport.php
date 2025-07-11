<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClientesResumenExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        $resumen = DB::table('Cat_Empleados')
            ->select('No_Tarjeta as Codigo_Cliente', DB::raw('COUNT(*) as Cuenta'))
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->whereNotNull('No_Tarjeta')
            ->groupBy('No_Tarjeta')
            ->get();

        $total = $resumen->sum('Cuenta');
        $resumen->push((object)[
            'Codigo_Cliente' => 'Total general',
            'Cuenta' => $total
        ]);

        return $resumen;
    }

    public function headings(): array
    {
        return ['Codigo Cliente', 'Cuenta de Código de Cliente'];
    }
}
