<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ConsultaConsumosExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
        // Limpia el buffer de salida para evitar problemas con el export
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();

        $noEmpleado = $this->request->get('NoEmpleado');
        $noEmpleado = ($noEmpleado === '' || $noEmpleado === 'null') ? null : $noEmpleado;

        $articuloFiltro = $this->request->get('Articulo');
        $articuloFiltro = ($articuloFiltro === '' || $articuloFiltro === 'null') ? null : $articuloFiltro;

        // Execute SP
        $rows = DB::select(
            'SET NOCOUNT ON;EXEC dbo.SP_Consulta_Consumos @Id_Planta = ?, @NoEmpleado = ?',
            [$this->idPlanta, $noEmpleado]
        );

        // Filter by Article in PHP if needed
        if ($articuloFiltro) {
            $rows = array_filter($rows, function ($r) use ($articuloFiltro) {
                $art = $r->Articulo ?? $r->articulo ?? '';
                return $art === $articuloFiltro;
            });
        }

        // Map and Calculate Status/Percentage
        $data = [];
        foreach ($rows as $r) {
            $r = (array) $r;

            $pmt = (int) ($r['Cantidad_Permitida'] ?? 0);
            $con = (int) ($r['Cantidad_Consumida'] ?? 0);
            $disp = (int) ($r['Disponible'] ?? 0);
            $base = $pmt ?: ($con + $disp) ?: 1;

            $pct = ($base > 0) ? min(100, round(($con / $base) * 100)) : 0;

            // Status Logic
            $status = 'OK';
            if ($con > $pmt && $pmt > 0) {
                $status = 'Excedido';
            } elseif ($pct >= 100) {
                $status = 'Agotado'; // Or 'Excedido' per JS logic if strict
            } elseif ($pct >= 80) {
                $status = 'Por agotar';
            }

            $data[] = [
                'No_Empleado' => $r['No_Empleado'] ?? '',
                'Nombre' => $r['Nombre'] ?? '',
                'Articulo' => $r['Articulo'] ?? '',
                'Frecuencia' => (int) ($r['Frecuencia'] ?? 0),
                'Cantidad_Permitida' => $pmt,
                'Cantidad_Consumida' => $con,
                'Disponible' => $disp,
                'Estado' => $status,
                'Uso_Porcentaje' => $pct . '%'
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'No. Empleado',
            'Nombre',
            'Artículo',
            'Frecuencia (días)',
            'Permitida',
            'Consumido',
            'Disponible',
            'Estado',
            '% Uso',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Logo and Title logic similar to other exports
                $sheet->getRowDimension(1)->setRowHeight(70);

                if (file_exists(public_path('vendor/adminlte/dist/img/vending-machine2.png'))) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $drawing->setPath(public_path('vendor/adminlte/dist/img/vending-machine2.png'));
                    $drawing->setHeight(70);
                    $drawing->setCoordinates('A1');
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'Reporte de Consulta de Consumos');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Move headings to row 2
                $sheet->insertNewRowBefore(2, 1);
                $sheet->fromArray($this->headings(), NULL, 'A2');
                $sheet->getStyle('A2:I2')->getFont()->setBold(true);

                // Formatting for Status Column (Column H)
                $highestRow = $sheet->getHighestRow();
                for ($row = 3; $row <= $highestRow; $row++) {
                    $status = $sheet->getCell('H' . $row)->getValue();
                    if ($status === 'Excedido' || $status === 'Agotado') {
                        $sheet->getStyle('H' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
                    } elseif ($status === 'Por agotar') {
                        $sheet->getStyle('H' . $row)->getFont()->setColor(new Color('FFA500')); // Orange
                    } else {
                        $sheet->getStyle('H' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
                    }
                }
            },
        ];
    }
}
