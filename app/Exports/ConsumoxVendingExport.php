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

class ConsumoxVendingExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
        ob_end_clean();
        ob_start();

        // Consulta para obtener la información agrupada por máquina (nombre), producto, área y fecha del último consumo
    $data = DB::table('Ctrl_Consumos')
    ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
    ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
    ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
    ->join('Ctrl_Mquinas', 'Ctrl_Consumos.Id_Maquina', '=', 'Ctrl_Mquinas.Id_Maquina') // Se une la tabla de máquinas
    ->where('Cat_Empleados.Id_Planta', $this->idPlanta)
    ->groupBy(
        'Ctrl_Mquinas.Txt_Nombre', // Se agrupa por el nombre de la máquina
        'Ctrl_Consumos.Id_Articulo', 
        'Cat_Articulos.Txt_Descripcion', 
        'Cat_Articulos.Txt_Codigo_Cliente', 
        'Cat_Articulos.Txt_Codigo', 
        'Cat_Area.Txt_Nombre'
    )
    ->select(
        'Ctrl_Mquinas.Txt_Nombre as Maquina', // Se selecciona el nombre de la máquina
        DB::raw('COUNT(Ctrl_Consumos.Id_Articulo) as Total_Consumos'), // Total de consumos del producto en la vending
        DB::raw('COUNT(DISTINCT Ctrl_Consumos.Id_Empleado) as No_Empleados'), // Número de empleados distintos consumiendo el producto
        'Cat_Articulos.Txt_Descripcion as Producto',
        'Cat_Articulos.Txt_Codigo_Cliente as Codigo_Cliente',
        'Cat_Articulos.Txt_Codigo as Codigo_Urvina',
        'Cat_Area.Txt_Nombre as Area', // Nombre del área
        DB::raw('MAX(Ctrl_Consumos.Fecha_Consumo) as Ultimo_Consumo') // Fecha del último consumo
    );

        // Aplicar filtros
        if ($this->request->filled('area')) {
            $data->where('Cat_Area.Txt_Nombre', 'like', "%{$this->request->area}%");
        }

        if ($this->request->filled('vending')) {
            $data->where('Ctrl_Consumos.Id_Maquina', '=', "{$this->request->vending}");
        }

        if ($this->request->filled('product')) {
            $data->where(function($query) {
                $query->where('Cat_Articulos.Txt_Descripcion', 'like', "%{$this->request->product}%")
                    ->orWhere('Cat_Articulos.Txt_Codigo', 'like', "%{$this->request->product}%")
                    ->orWhere('Cat_Articulos.Txt_Codigo_Cliente', 'like', "%{$this->request->product}%");
            });
        }

        if ($this->request->filled('dateRange')) {
            $dates = explode(' - ', $this->request->input('dateRange'));
    
            // Verificar si se han proporcionado exactamente dos fechas
            if (count($dates) === 2) {
                $startDate = trim($dates[0]);
                $endDate = trim($dates[1]);
    
                // Verificar que las fechas no estén vacías
                if (!empty($startDate) && !empty($endDate)) {
                    $startDate = date('Y-m-d', strtotime($startDate));
                    $endDate = date('Y-m-d', strtotime($endDate));
    
                    // Asegurarse de que las fechas sean válidas
                    if ($startDate && $endDate && $startDate <= $endDate) {
                        $data->whereBetween('Ctrl_Consumos.Fecha_Real', [$startDate, $endDate]);
                    } else {
                        // Si las fechas no son válidas, mostrar un mensaje de error y no aplicar el filtro de fechas
                        //dd('Fechas no válidas:', $dates);
                    }
                } else {
                    // Si alguna de las fechas está vacía, mostrar un mensaje de error y no aplicar el filtro de fechas
                    //dd('Fechas vacías:', $dates);
                }
            } else {
                // Si el formato de dateRange no es válido, mostrar un mensaje de error y no aplicar el filtro de fechas
                //dd('El formato de dateRange no es válido:', $this->request->input('dateRange'));
            }
        }
        
        return $data->get();
    }

    public function headings(): array
    {
        return [
            'VM',
            'Consumo (veces)',
            'Empleados Consumiendo',
            'Producto',
            'Código Cliente',
            'Código Urvina',
            'Área',
            'Ultimo Consumo',
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
                $sheet->setCellValue('A1', 'Reporte de Consumos por Vending Machine');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Mover encabezado a la fila 2
                $sheet->insertNewRowBefore(2, 1); // Insertar una fila en la fila 2
                $sheet->fromArray($this->headings(), NULL, 'A2'); // Agregar encabezados en la fila 2

                // Encabezado en negritas
                $sheet->getStyle('B2:H2')->getFont()->setBold(true);

                // Mensajes en el footer
                $highestRow = $sheet->getHighestRow();
                $sheet->mergeCells('B' . ($highestRow + 3) . ':H' . ($highestRow + 3));
                $sheet->setCellValue('B' . ($highestRow + 3), 'En consumos esta deshabilitada la edición o subida masiva. Siga su manual de Usuario.');
                $sheet->getStyle('B' . ($highestRow + 3))->getFont()->getColor()->setRGB('FF0000');
                $sheet->getStyle('B' . ($highestRow + 3))->getFont()->setSize(10);
            },
        ];
    }
}
