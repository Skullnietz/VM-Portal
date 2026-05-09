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

class AreasExportAdmin implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $idPlanta;

    public function __construct($idPlanta)
    {
        $this->idPlanta = $idPlanta;
    }

    public function collection()
    {
        ob_end_clean();
        ob_start();
        
        $areas = DB::table('Cat_Area')
            ->select('Txt_Nombre', 'Txt_Estatus', 'Fecha_Alta', 'Id_Area')
            ->where('Id_Planta', $this->idPlanta)
            ->get();

        foreach ($areas as $area) {
            $area->Num_Empleados = DB::table('Cat_Empleados')
                ->where('Id_Area', $area->Id_Area)
                ->where('Id_Planta', $this->idPlanta)
                ->count();

            $area->Num_Permisos = DB::table('Ctrl_Permisos_x_Area')
                ->where('Id_Area', $area->Id_Area)
                ->where('Id_Planta', $this->idPlanta)
                ->count();
        }

        $areas = $areas->map(function ($area) {
            return [
                'Nombre' => $area->Txt_Nombre,
                'Estatus' => $area->Txt_Estatus,
                'No_Empleados' => $area->Num_Empleados,
                'No_Permisos' => $area->Num_Permisos,
                'Fecha_Registro' => $area->Fecha_Alta,
            ];
        });

        return collect($areas);
    }

    public function headings(): array
    {
        return [
            'Nombre del Área',
            'Estatus del Área',
            'Número de Empleados',
            'Número de Permisos',
            'Fecha de Registro',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Ajustar altura de la celda A1 para el logo
                $sheet->getRowDimension(1)->setRowHeight(70);

                // Logo
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath(public_path('vendor/adminlte/dist/img/vending-machine2.png')); // Ruta al logo
                $drawing->setHeight(70);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);

                // Título
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'Reporte de Áreas');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Mover encabezado a la fila 2
                $sheet->insertNewRowBefore(2, 1); // Insertar una fila en la fila 2
                $sheet->fromArray($this->headings(), NULL, 'A2'); // Agregar encabezados en la fila 2

                // Encabezado en negritas
                $sheet->getStyle('A2:E2')->getFont()->setBold(true);

                // Mensajes en el footer
                $highestRow = $sheet->getHighestRow();
                $sheet->mergeCells('A' . ($highestRow + 3) . ':E' . ($highestRow + 3));
                $sheet->setCellValue('A' . ($highestRow + 3), 'En areas esta deshabilitado la edición o subida masiva. Siga su manual de Usuario.');
                $sheet->getStyle('A' . ($highestRow + 3))->getFont()->getColor()->setRGB('FF0000');
                $sheet->getStyle('A' . ($highestRow + 3))->getFont()->setSize(10);
            },
        ];
    }
}
