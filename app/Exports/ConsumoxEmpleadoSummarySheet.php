<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ConsumoxEmpleadoSummarySheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
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
        // Base query similar to the main export but grouped
        $query = DB::table('Ctrl_Consumos')
            ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
            ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
            ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->leftJoin(DB::raw('(
                select b.Id_Maquina, b.Talla, c.Codigo_Clientte as Txt_Codigo_Cliente, a.Id_Articulo, a.Id_Consumo, d.Txt_Descripcion, d.Txt_Codigo 
                from Ctrl_Consumos as a
                inner join Configuracion_Maquina as b on a.Id_Maquina= b.Id_Maquina and a.Seleccion = b.Seleccion 
                right join Codigos_Clientes as c on b.Id_Articulo= c.Id_Articulo and b.Talla = c.Talla
                inner join Cat_Articulos as d on a.Id_Articulo = d.Id_Articulo 
            ) as z'), 'Ctrl_Consumos.Id_Consumo', '=', 'z.Id_Consumo')
            ->where('Cat_Empleados.Id_Planta', $this->idPlanta)
            ->select(
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.talla,'') as Producto"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo) as Codigo_Urvina"),
                DB::raw("SUM(Ctrl_Consumos.Cantidad) as Total_Cantidad")
            )
            ->groupBy(
                DB::raw("isnull(z.Txt_Descripcion, Cat_Articulos.Txt_Descripcion) + ' ' + isnull(z.talla,'')"),
                DB::raw("isnull(z.Txt_Codigo, Cat_Articulos.Txt_Codigo)")
            )
            ->orderByDesc('Total_Cantidad');

        // Apply Date Filters
        if ($this->request->filled('dateRange')) {
            $dates = explode(' - ', $this->request->input('dateRange'));
            if (count($dates) === 2) {
                $startDate = date('Y-m-d', strtotime(trim($dates[0])));
                $endDate = date('Y-m-d', strtotime(trim($dates[1])));
                $query->whereBetween('Ctrl_Consumos.Fecha_Consumo', [$startDate, $endDate]);
            }
        }

        // Also apply individual starDate/endDate if dateRange is empty (covering operator report case)
        if ($this->request->filled('startDate') && $this->request->filled('endDate')) {
            $startDate = $this->request->startDate . ' 00:00:00';
            $endDate = $this->request->endDate . ' 23:59:59';
            $query->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Código Urvina',
            'Cantidad Total'
        ];
    }

    public function title(): string
    {
        return 'Resumen por Artículo';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:C1')->getFont()->setBold(true);
                $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
