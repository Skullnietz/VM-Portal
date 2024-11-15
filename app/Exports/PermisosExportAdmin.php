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

class PermisosExportAdmin implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
        
        
        $permisos = DB::table('Ctrl_Permisos_x_Area')
            ->select('Id_Permiso', 'Id_Area', 'Id_Articulo', 'Frecuencia', 'Cantidad', 'Status')
            ->where('Id_Planta', $this->idPlanta)
            ->get();

        foreach ($permisos as $permiso) {
            $permiso->NArea = DB::table('Cat_Area')->where('Id_Area', $permiso->Id_Area)->value('Txt_Nombre');
            $articulo = DB::table('Cat_Articulos')->where('Id_Articulo', $permiso->Id_Articulo)->first(['Txt_Descripcion', 'Txt_Codigo', 'Txt_Codigo_Cliente']);
            $permiso->DescripcionArticulo = $articulo->Txt_Descripcion;
            $permiso->CodigoArticulo = $articulo->Txt_Codigo;
            $permiso->CodigoCliente = $articulo->Txt_Codigo_Cliente;
        }

        $permisos = $permisos->map(function ($permiso) {
            return [
                'Id_Permiso' => $permiso->Id_Permiso,
                'NArea' => $permiso->NArea,
                'DescripcionArticulo' => $permiso->DescripcionArticulo,
                'CodigoArticulo' => $permiso->CodigoArticulo,
                'CodigoCliente' => $permiso->CodigoCliente,
                'Frecuencia' => $permiso->Frecuencia,
                'Cantidad' => $permiso->Cantidad,
                'Status' => $permiso->Status,
            ];
        });

        return collect($permisos);
    }

    public function headings(): array
    {
        return [
            'Id_Permiso', // Esta columna será oculta en Excel
            'Area',
            'Descripción del Artículo',
            'Código Urvina',
            'Código de Cliente',
            'Frecuencia en Días',
            'Cantidad en Pzas',
            'Estatus de Permiso',
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
                $sheet->setCellValue('A1', 'Reporte de Permisos');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Mover encabezado a la fila 2
                $sheet->insertNewRowBefore(2, 1); // Insertar una fila en la fila 2
                $sheet->fromArray($this->headings(), NULL, 'A2'); // Agregar encabezados en la fila 2

                // Ocultar la columna Id_Permiso
                $sheet->getColumnDimension('A')->setVisible(false);

                // Encabezado en negritas
                $sheet->getStyle('B2:H2')->getFont()->setBold(true);

                // Mensajes en el footer
                $highestRow = $sheet->getHighestRow();
                $sheet->mergeCells('B' . ($highestRow + 3) . ':H' . ($highestRow + 3));
                $sheet->setCellValue('B' . ($highestRow + 3), 'En permisos esta deshabilitado la edición o subida masiva. Siga su manual de Usuario.');
                $sheet->getStyle('B' . ($highestRow + 3))->getFont()->getColor()->setRGB('FF0000');
                $sheet->getStyle('B' . ($highestRow + 3))->getFont()->setSize(10);
            },
        ];
    }
}
