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

class MissingItemsDetailSheet implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    protected $idMaquina;
    protected $nombreMaquina;

    public function __construct($idMaquina, $nombreMaquina)
    {
        $this->idMaquina = $idMaquina;
        $this->nombreMaquina = $nombreMaquina;
    }

    public function collection()
    {
        // Obtener artículos faltantes
        $faltantes = DB::table('Configuracion_Maquina')
            ->join('Cat_Articulos', 'Configuracion_Maquina.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
            ->select(
                'Cat_Articulos.Txt_Descripcion',
                'Cat_Articulos.Txt_Codigo',
                'Configuracion_Maquina.Num_Charola',
                'Configuracion_Maquina.Seleccion',
                'Configuracion_Maquina.Stock',
                'Configuracion_Maquina.Cantidad_Max',
                DB::raw('(Configuracion_Maquina.Cantidad_Max - Configuracion_Maquina.Stock) as Faltante')
            )
            ->where('Configuracion_Maquina.Id_Maquina', $this->idMaquina)
            ->whereRaw('Configuracion_Maquina.Stock < Configuracion_Maquina.Cantidad_Max')
            ->get();

        return $faltantes;
    }

    public function headings(): array
    {
        return [
            'Artículo',
            'Código',
            'Charola',
            'Selección',
            'Stock Actual',
            'Capacidad Máxima',
            'Cantidad Faltante',
        ];
    }

    public function title(): string
    {
        return 'Detalle Faltantes';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insertar filas para el título
                $sheet->insertNewRowBefore(1, 4);

                // Título
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'Reporte de Faltantes - ' . $this->nombreMaquina);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Fecha
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Estilo para los encabezados de la tabla (fila 5)
                $sheet->getStyle('A5:G5')->getFont()->setBold(true);
                $sheet->getStyle('A5:G5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCCCCCC');

                // Bordes para la tabla
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A5:G' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
