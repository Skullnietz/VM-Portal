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

class EmpleadosExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    public function collection()
    {
        ob_end_clean();
        ob_start();
        session_start();
        
        $empleados = DB::table('Cat_Empleados')
            ->select('No_Empleado', 'Nip','No_Tarjeta', 'Nombre', 'APaterno', 'AMaterno', 'Id_Area', 'Txt_Estatus')
            ->where('Id_Planta', $_SESSION['usuario']->Id_Planta)
            ->get();

        foreach ($empleados as $empleado) {
            $empleado->NArea = DB::table('Cat_Area')->where('Id_Area', $empleado->Id_Area)->value('Txt_Nombre');
        }

        $empleados = $empleados->map(function ($empleado) {
            return [
                'No_Empleado' => $empleado->No_Empleado,
                'Nip' => $empleado->Nip,
                'No_Tarjeta' => $empleado->No_Tarjeta,
                'Nombre' => $empleado->Nombre,
                'APaterno' => $empleado->APaterno,
                'AMaterno' => $empleado->AMaterno,
                'NArea' => $empleado->NArea,
                'Txt_Estatus' => $empleado->Txt_Estatus,
            ];
        });

        return collect($empleados);
    }

    public function headings(): array
    {
        return [
            'No_Empleado',
            'NIP',
            'ID Tarjeta',
            'Nombre',
            'Apellido Paterno',
            'Apellido Materno',
            'Area',
            'Estatus',
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
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Reporte de empleados');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Mover encabezado a la fila 2
                $sheet->insertNewRowBefore(2, 1); // Insertar una fila en la fila 2
                $sheet->fromArray($this->headings(), NULL, 'A2'); // Agregar encabezados en la fila 2

                // Encabezado en negritas
                $sheet->getStyle('A2:H2')->getFont()->setBold(true);

                // Mensajes en el footer
                $highestRow = $sheet->getHighestRow();
                $sheet->mergeCells('A' . ($highestRow + 3) . ':H' . ($highestRow + 3));
                $sheet->setCellValue('A' . ($highestRow + 3), 'Este formato no es un CSV, por lo que no es valido para subir a portal. Siga su manual de Usuario.');
                $sheet->getStyle('A' . ($highestRow + 3))->getFont()->getColor()->setRGB('FF0000');
                $sheet->getStyle('A' . ($highestRow + 3))->getFont()->setSize(10);
            },
        ];
    }
}
