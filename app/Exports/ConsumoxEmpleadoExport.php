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

class ConsumoxEmpleadoExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
    ob_end_clean();
    ob_start();
    
    // Construye la consulta base
    $data = DB::table('Ctrl_Consumos')
        ->join('Cat_Empleados', 'Ctrl_Consumos.Id_Empleado', '=', 'Cat_Empleados.Id_Empleado')
        ->join('Cat_Area', 'Cat_Empleados.Id_Area', '=', 'Cat_Area.Id_Area')
        ->join('Cat_Articulos', 'Ctrl_Consumos.Id_Articulo', '=', 'Cat_Articulos.Id_Articulo')
        ->where('Cat_Empleados.Id_Planta', $this->idPlanta)
        ->select(
            DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno) as Nombre"),
            'Cat_Empleados.No_Empleado as Numero_de_empleado',
            'Cat_Area.Txt_Nombre as Area',
            'Cat_Articulos.Txt_Descripcion as Producto',
            'Cat_Articulos.Txt_Codigo as Codigo_Urvina',
            'Cat_Articulos.Txt_Codigo_Cliente as Codigo_Cliente',
            'Ctrl_Consumos.Fecha_Consumo as Fecha',
            'Ctrl_Consumos.Cantidad'
        );

    // Aplicar filtros si están presentes
    if ($this->request->filled('area')) {
        $areas = $this->request->input('area');
        if (is_array($areas)) {
            $areas = array_filter($areas, function($value) {
                return !empty($value) && $value !== 'null';
            });
            if (!empty($areas)) {
                if (count($areas) > 1) {
                    $data->whereIn('Cat_Area.Txt_Nombre', $areas);
                } else {
                    $data->where('Cat_Area.Txt_Nombre', 'like', "%{$areas[0]}%");
                }
            }
        } elseif (is_string($areas) && !empty($areas) && $areas !== 'null') {
            $areaArray = array_filter(array_map('trim', explode(',', $areas)), function($value) {
                return !empty($value);
            });
            if (!empty($areaArray)) {
                if (count($areaArray) > 1) {
                    $data->whereIn('Cat_Area.Txt_Nombre', $areaArray);
                } else {
                    $data->where('Cat_Area.Txt_Nombre', 'like', "%{$areaArray[0]}%");
                }
            }
        }
    }

    if ($this->request->filled('product')) {
        $products = $this->request->input('product');
        if (is_array($products)) {
            $products = array_filter($products, function($value) {
                return !empty($value) && $value !== 'null';
            });
            if (!empty($products)) {
                if (count($products) > 1) {
                    $data->whereIn('Cat_Articulos.Txt_Descripcion', $products);
                } else {
                    $data->where('Cat_Articulos.Txt_Descripcion', 'like', "%{$products[0]}%");
                }
            }
        } elseif (is_string($products) && !empty($products) && $products !== 'null') {
            $productArray = array_filter(array_map('trim', explode(',', $products)), function($value) {
                return !empty($value);
            });
            if (!empty($productArray)) {
                if (count($productArray) > 1) {
                    $data->whereIn('Cat_Articulos.Txt_Descripcion', $productArray);
                } else {
                    $data->where('Cat_Articulos.Txt_Descripcion', 'like', "%{$productArray[0]}%");
                }
            }
        }
    }

    if ($this->request->filled('employee')) {
        $selectedEmployees = $this->request->input('employee');
        if (is_array($selectedEmployees)) {
            $selectedEmployees = array_filter($selectedEmployees, function($value) {
                return !empty($value) && $value !== 'null';
            });
            if (!empty($selectedEmployees)) {
                if (count($selectedEmployees) > 1) {
                    $data->whereIn(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), $selectedEmployees);
                } else {
                    $data->where(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), 'like', "%{$selectedEmployees[0]}%");
                }
            }
        } elseif (is_string($selectedEmployees) && !empty($selectedEmployees) && $selectedEmployees !== 'null') {
            $employeeArray = array_filter(array_map('trim', explode(',', $selectedEmployees)), function($value) {
                return !empty($value);
            });
            if (!empty($employeeArray)) {
                if (count($employeeArray) > 1) {
                    $data->whereIn(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), $employeeArray);
                } else {
                    $data->where(DB::raw("CONCAT(Cat_Empleados.Nombre, ' ', Cat_Empleados.APaterno, ' ', Cat_Empleados.AMaterno)"), 'like', "%{$employeeArray[0]}%");
                }
            }
        }
    }

    if ($this->request->filled('dateRange')) {
        $dates = explode(' - ', $this->request->input('dateRange'));
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
            'Empleado', // Esta columna será oculta en Excel
            'No.Empleado',
            'Area',
            'Descripción del Artículo',
            'Código de Cliente',
            'Código de Urvina',
            'Fecha de Consumo',
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
                $sheet->setCellValue('A1', 'Reporte de Consumos por Empleado');
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