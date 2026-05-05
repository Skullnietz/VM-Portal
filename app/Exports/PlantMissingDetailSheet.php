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

class PlantMissingDetailSheet implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
        $rows = DB::table('Configuracion_Maquina AS cm')
            ->join('Ctrl_Mquinas AS maq', 'cm.Id_Maquina', '=', 'maq.Id_Maquina')
            ->leftJoin('Cat_Articulos AS art', 'cm.Id_Articulo', '=', 'art.Id_Articulo')
            ->where('maq.Id_Planta', $this->idPlanta)
            ->where('maq.Txt_Estatus', 'Alta')
            ->orderBy('maq.Txt_Nombre')
            ->orderBy('cm.Num_Charola')
            ->orderBy('cm.Seleccion')
            ->select(
                'maq.Txt_Nombre AS Vending',
                'art.Txt_Descripcion',
                'art.Txt_Codigo',
                'cm.Talla',
                'cm.Num_Charola',
                'cm.Seleccion',
                'cm.Stock',
                'cm.Cantidad_Max',
                'art.Tamano_Espiral',
                DB::raw('(cm.Cantidad_Max - cm.Stock) AS Faltante')
            )
            ->get();

        // Misma lógica de SELECCIÓN VACÍA / OCUPADA que MissingItemsDetailSheet,
        // pero reseteando por cada cambio de vending.
        $lastVending       = null;
        $lastTamanoEspiral = null;
        $lastCharola       = null;

        $rows->transform(function ($item) use (&$lastVending, &$lastTamanoEspiral, &$lastCharola) {
            if ($lastVending !== $item->Vending) {
                $lastVending       = $item->Vending;
                $lastTamanoEspiral = null;
                $lastCharola       = null;
            }

            if ($lastCharola !== $item->Num_Charola) {
                $lastTamanoEspiral = null;
                $lastCharola       = $item->Num_Charola;
            }

            if (empty($item->Txt_Descripcion)) {
                $item->Txt_Descripcion = $lastTamanoEspiral === 'Grande'
                    ? 'SELECCIÓN OCUPADA'
                    : 'SELECCIÓN VACÍA';
                $item->Stock       = '';
                $item->Cantidad_Max = '';
                $item->Faltante    = '';
            }

            $lastTamanoEspiral = $item->Tamano_Espiral;
            unset($item->Tamano_Espiral);

            return $item;
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Vending',
            'Artículo',
            'Código',
            'Talla',
            'Charola',
            'Selección',
            'Stock Actual',
            'Capacidad Máxima',
            'Cantidad Faltante',
        ];
    }

    public function title(): string
    {
        return 'Detalle por Selección';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 4);

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'Detalle por Selección — ' . $this->nombrePlanta);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $last = $sheet->getHighestRow();

                $sheet->getStyle('A5:I5')->getFont()->setBold(true);
                $sheet->getStyle('A5:I5')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1F4E79');
                $sheet->getStyle('A5:I5')->getFont()->getColor()->setARGB('FFFFFFFF');

                $sheet->getStyle('A5:I' . $last)->getBorders()
                    ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Sombrear grupos de vending alternados + marcar selecciones vacías/ocupadas
                $currentVending = null;
                $shade = false;
                for ($r = 6; $r <= $last; $r++) {
                    $vending    = $sheet->getCell("A{$r}")->getValue();
                    $articulo   = $sheet->getCell("B{$r}")->getValue();

                    if ($vending !== $currentVending) {
                        $currentVending = $vending;
                        $shade = !$shade;
                    }

                    if ($articulo === 'SELECCIÓN VACÍA' || $articulo === 'SELECCIÓN OCUPADA') {
                        $sheet->getStyle("A{$r}:I{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFCCCC');
                    } elseif ($shade) {
                        $sheet->getStyle("A{$r}:I{$r}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFD6E4F0');
                    }
                }
            },
        ];
    }
}
