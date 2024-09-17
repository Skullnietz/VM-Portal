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
        // Aplicar filtros si están presentes
if ($this->request->filled('area')) {
    $areas = $this->request->input('area');
    
    if (is_array($areas)) {
        // Limpia el array para eliminar valores nulos o vacíos
        $areas = array_filter($areas, function($value) {
            return $value !== null && $value !== '' && $value !== 'null';
        });

        // Si después de filtrar el array no está vacío, aplica el filtro
        if (!empty($areas)) {
            if (count($areas) > 1) {
                $data->whereIn('Cat_Area.Txt_Nombre', $areas);
            } else {
                $data->where('Cat_Area.Txt_Nombre', $areas[0]);
            }
        }
    } elseif (is_string($areas) && $areas !== 'null' && $areas !== '') {
        // Si se recibe como cadena y no es 'null' ni vacío
        $areaArray = explode(',', $areas);
        $areas = array_map('trim', $areaArray);
        
        // Limpia el array para eliminar valores vacíos
        $areas = array_filter($areas, function($value) {
            return $value !== '';
        });

        // Si después de filtrar el array no está vacío, aplica el filtro
        if (!empty($areas)) {
            if (count($areas) > 1) {
                $data->whereIn('Cat_Area.Txt_Nombre', $areas);
            } else {
                $data->where('Cat_Area.Txt_Nombre', $areas[0]);
            }
        }
    }
}

if ($this->request->filled('product')) {
    $products = $this->request->input('product');
    
    if (is_array($products)) {
        // Limpia el array para eliminar valores nulos o vacíos
        $products = array_filter($products, function($value) {
            return $value !== null && $value !== '' && $value !== 'null';
        });

        // Si después de filtrar el array no está vacío, aplica el filtro
        if (!empty($products)) {
            if (count($products) > 1) {
                $data->whereIn('Cat_Articulos.Txt_Descripcion', $products);
            } else {
                $data->where('Cat_Articulos.Txt_Descripcion', $products[0]);
            }
        }
    } elseif (is_string($products) && $products !== 'null' && $products !== '') {
        // Si se recibe como cadena y no es 'null' ni vacío
        $productArray = explode(',', $products);
        $products = array_map('trim', $productArray);
        
        // Limpia el array para eliminar valores vacíos
        $products = array_filter($products, function($value) {
            return $value !== '';
        });

        // Si después de filtrar el array no está vacío, aplica el filtro
        if (!empty($products)) {
            if (count($products) > 1) {
                $data->whereIn('Cat_Articulos.Txt_Descripcion', $products);
            } else {
                $data->where('Cat_Articulos.Txt_Descripcion', $products[0]);
            }
        }
    }
}

    $vending = $this->request->input('vending');

if (is_array($vending)) {
    // Limpia el array para eliminar valores nulos o vacíos
    $vending = array_filter($vending, function($value) {
        return $value !== null && $value !== '';
    });

    // Si después de filtrar el array no está vacío, aplica el filtro
    if (!empty($vending)) {
        if (count($vending) > 1) {
            
            $data->whereIn('Ctrl_Consumos.Id_Maquina', $vending);
        } else {
            $data->where('Ctrl_Consumos.Id_Maquina', $vending[0]);
        }
    }
}

    if ($this->request->filled('dateRange')) {
        $dates = explode(' - ', $this->request->input('dateRange', ''));
    
        if (count($dates) === 2) {
            $startDate = trim($dates[0]);
            $endDate = trim($dates[1]);
    
            if (!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d', strtotime($startDate));
                $endDate = date('Y-m-d', strtotime($endDate));
    
                if ($startDate && $endDate && $startDate <= $endDate) {
                    $data->whereBetween('Ctrl_Consumos.Fecha_Consumo', [$startDate, $endDate]);
                }
            }
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
