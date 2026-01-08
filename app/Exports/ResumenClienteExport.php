<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResumenClienteExport implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle
{
    protected $request;
    protected $idPlanta;

    public function __construct($request, $idPlanta)
    {
        $this->request = $request;
        $this->idPlanta = $idPlanta;
    }

    public function collection()
    {
        $query = DB::table('Ctrl_Consumos')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->where('Cat_Empleados.Id_Planta', $this->idPlanta)
            ->select(
                'Cat_Articulos.Txt_Codigo_Cliente as Codigo_Cliente',
                DB::raw('SUM(Ctrl_Consumos.Cantidad) as Total')
            )
            ->groupBy('Cat_Articulos.Txt_Codigo_Cliente');

        // Repite la lógica de filtros como en ConsumoxEmpleadoExport
        if ($this->request->filled('dateRange')) {
            $dates = explode(' - ', $this->request->input('dateRange'));
            if (count($dates) === 2) {
                $startDate = trim($dates[0]);
                $endDate = trim($dates[1]);
                $startDate = date('Y-m-d', strtotime($startDate));
                $endDate = date('Y-m-d', strtotime($endDate));
                if ($startDate && $endDate && $startDate <= $endDate) {
                    $query->whereBetween('Ctrl_Consumos.Fecha_Consumo', [$startDate, $endDate]);
                }
            }
        }

        $datos = $query->get();

        // Total general
        $totalGeneral = $datos->sum('Total');
        $datos->push((object) [
            'Codigo_Cliente' => 'Total general',
            'Total' => $totalGeneral
        ]);

        return $datos;
    }

    public function headings(): array
    {
        return ['Codigo Cliente', 'Cuenta de Código de Cliente'];
    }

    public function title(): string
    {
        return 'Resumen por cliente';
    }
}
