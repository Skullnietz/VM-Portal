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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PlantMissingByVendingSheet implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    protected $idPlanta;
    protected $nombrePlanta;

    public function __construct($idPlanta, $nombrePlanta)
    {
        $this->idPlanta    = $idPlanta;
        $this->nombrePlanta = $nombrePlanta;
    }

    public function collection()
    {
        return DB::table('Configuracion_Maquina AS cm')
            ->join('Ctrl_Mquinas AS maq', 'cm.Id_Maquina', '=', 'maq.Id_Maquina')
            ->join('Cat_Articulos AS art', 'cm.Id_Articulo', '=', 'art.Id_Articulo')
            ->where('maq.Id_Planta', $this->idPlanta)
            ->where('maq.Txt_Estatus', 'Alta')
            ->whereRaw('cm.Stock < cm.Cantidad_Max')
            ->groupBy('maq.Txt_Nombre', 'art.Txt_Descripcion', 'art.Txt_Codigo', 'cm.Talla')
            ->orderBy('maq.Txt_Nombre')
            ->orderBy('art.Txt_Descripcion')
            ->select(
                'maq.Txt_Nombre AS Vending',
                'art.Txt_Descripcion AS Articulo',
                'art.Txt_Codigo AS Codigo',
                'cm.Talla',
                DB::raw('SUM(cm.Cantidad_Max - cm.Stock) AS Faltante')
            )
            ->get();
    }

    public function headings(): array
    {
        return ['Vending', 'Artículo', 'Código', 'Talla', 'Faltante'];
    }

    public function title(): string
    {
        return 'Por Vending';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 4);

                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'Faltantes por Vending — ' . $this->nombrePlanta);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $last = $sheet->getHighestRow();

                $sheet->getStyle('A5:E5')->getFont()->setBold(true);
                $sheet->getStyle('A5:E5')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1F4E79');
                $sheet->getStyle('A5:E5')->getFont()->getColor()->setARGB('FFFFFFFF');

                $sheet->getStyle('A5:E' . $last)->getBorders()
                    ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Sombrear filas de cambio de vending
                $currentVending = null;
                $shade = false;
                for ($r = 6; $r <= $last; $r++) {
                    $vending = $sheet->getCell("A{$r}")->getValue();
                    if ($vending !== $currentVending) {
                        $currentVending = $vending;
                        $shade = !$shade;
                    }
                    if ($shade) {
                        $sheet->getStyle("A{$r}:E{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFD6E4F0');
                    }
                }

                $sheet->getStyle('E6:E' . $last)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
