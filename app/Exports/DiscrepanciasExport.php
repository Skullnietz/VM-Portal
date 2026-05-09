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

class DiscrepanciasExport implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    protected $idMaquina;
    protected $nombreMaquina;

    public function __construct($idMaquina)
    {
        $this->idMaquina = $idMaquina;
        $this->nombreMaquina = DB::table('Ctrl_Mquinas')
            ->where('Id_Maquina', $idMaquina)
            ->value('Txt_Nombre') ?? 'Máquina';
    }

    public function collection()
    {
        $data = DB::select("
            SELECT
                cm.Seleccion,
                cm.Num_Charola AS Charola,
                ca.Txt_Descripcion AS Articulo,
                cm.Talla,
                ISNULL(hr.Cantidad_Nueva, cm.Stock) AS Stock_Ultimo_Relleno,
                ISNULL(consumos.Total_Consumido, 0) AS Consumos_Registrados,
                (ISNULL(hr.Cantidad_Nueva, cm.Stock) - ISNULL(consumos.Total_Consumido, 0)) AS Stock_Teorico,
                cm.Stock AS Stock_Actual,
                (ISNULL(hr.Cantidad_Nueva, cm.Stock) - ISNULL(consumos.Total_Consumido, 0) - cm.Stock) AS Discrepancia
            FROM Configuracion_Maquina cm
            INNER JOIN Cat_Articulos ca ON cm.Id_Articulo = ca.Id_Articulo
            OUTER APPLY (
                SELECT TOP 1 h.Cantidad_Nueva, h.Fecha_Relleno
                FROM Historial_Relleno h
                WHERE h.Id_Configuracion = cm.Id_Configuracion
                ORDER BY h.Fecha_Relleno DESC
            ) hr
            OUTER APPLY (
                SELECT SUM(c.Cantidad) AS Total_Consumido
                FROM Ctrl_Consumos c
                WHERE c.Id_Maquina = cm.Id_Maquina
                  AND c.Seleccion = cm.Seleccion
                  AND c.Fecha_Real >= ISNULL(hr.Fecha_Relleno, '2000-01-01')
            ) consumos
            WHERE cm.Id_Maquina = ?
              AND cm.Id_Articulo IS NOT NULL
            ORDER BY cm.Num_Charola, cm.Seleccion
        ", [$this->idMaquina]);

        return collect($data);
    }

    public function headings(): array
    {
        return ['Selección', 'Charola', 'Artículo', 'Talla', 'Último Relleno', 'Consumos', 'Stock Teórico', 'Stock Actual', 'Discrepancia'];
    }

    public function title(): string
    {
        return 'Discrepancias';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 4);

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'Reporte de Discrepancias - ' . $this->nombreMaquina);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5:I5')->getFont()->setBold(true);
                $sheet->getStyle('A5:I5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFCCCCCC');

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A5:I' . $highestRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Colorear discrepancias
                for ($row = 6; $row <= $highestRow; $row++) {
                    $disc = abs((int)$sheet->getCell('I' . $row)->getValue());
                    if ($disc >= 3) {
                        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFCCCC'); // Rojo claro
                    } elseif ($disc >= 1) {
                        $sheet->getStyle('A' . $row . ':I' . $row)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFFFCC'); // Amarillo claro
                    }
                }
            },
        ];
    }
}
