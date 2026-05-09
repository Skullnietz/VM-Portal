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

class CortePreDetailSheet implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
            ->leftJoin('Cat_Articulos', 'Corte_Detalle.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Corte_Detalle.Seleccion',
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo',
                'Corte_Detalle.Talla',
                'Corte_Detalle.Stock_Actual',
                'Corte_Detalle.Cantidad_Max',
                'Corte_Detalle.Cantidad_Necesaria'
            )
            ->where('Corte_Detalle.Id_Corte', $this->idCorte)
            ->orderBy('Corte_Detalle.Seleccion')
            ->get();
    }

    public function headings(): array
    {
        return ['Selección', 'Artículo', 'Código', 'Talla', 'Stock Actual', 'Máximo', 'Cantidad Necesaria'];
    }

    public function title(): string
    {
        return 'Detalle Corte';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 4);

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'Corte Pre-Resurtimiento - ' . $this->nombreMaquina);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5:G5')->getFont()->setBold(true);
                $sheet->getStyle('A5:G5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCCCCCC');

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A5:G' . $highestRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Resaltar filas con faltantes
                for ($row = 6; $row <= $highestRow; $row++) {
                    $necesaria = $sheet->getCell('G' . $row)->getValue();
                    if ($necesaria > 0) {
                        $sheet->getStyle('G' . $row)->getFont()->setBold(true);
                        $sheet->getStyle('G' . $row)->getFont()->getColor()->setARGB('FFCC0000');
                    }
                }
            },
        ];
    }
}
