<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CortePreSummarySheet implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    protected $idCorte;
    protected $nombreMaquina;

    public function __construct($idCorte, $nombreMaquina)
    {
        $this->idCorte = $idCorte;
        $this->nombreMaquina = $nombreMaquina;
    }

    public function collection()
    {
        return DB::table('Corte_Detalle')
            ->join('Cat_Articulos', 'Corte_Detalle.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo',
                'Corte_Detalle.Talla',
                DB::raw('SUM(Corte_Detalle.Cantidad_Necesaria) as Total_Necesario')
            )
            ->where('Corte_Detalle.Id_Corte', $this->idCorte)
            ->where('Corte_Detalle.Cantidad_Necesaria', '>', 0)
            ->groupBy('Cat_Articulos.Txt_Descripcion', 'Cat_Articulos.Txt_Codigo', 'Corte_Detalle.Talla')
            ->get();
    }

    public function headings(): array
    {
        return ['Artículo', 'Código', 'Talla', 'Total Necesario'];
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
                $sheet->insertNewRowBefore(1, 4);

                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'Resumen Corte Pre - ' . $this->nombreMaquina);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5:D5')->getFont()->setBold(true);
                $sheet->getStyle('A5:D5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCCCCCC');

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A5:D' . $highestRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
