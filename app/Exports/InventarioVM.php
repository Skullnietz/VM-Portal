<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class InventarioVM implements WithMultipleSheets
{
    protected $idPlanta;

    public function __construct($idPlanta)
    {
        $this->idPlanta = $idPlanta;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Obtener todas las máquinas de la planta
        $maquinas = DB::table('Ctrl_Mquinas')
            ->where('Id_Planta', $this->idPlanta)
            ->get();

        // Crear una hoja por cada máquina
        foreach ($maquinas as $maquina) {
            $sheets[] = new MaquinaSheet($maquina->Id_Maquina, $maquina->Txt_Nombre);
        }

        return $sheets;
    }
}

class MaquinaSheet implements FromCollection, WithTitle, WithHeadings, WithEvents
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
        ob_end_clean();
        ob_start();

        // Obtener la configuración y el stock de la máquina
        $configuracion = DB::table('Configuracion_Maquina')
            ->select('Id_Articulo', 'Id_Maquina', DB::raw('SUM(Cantidad_Max) as Total_Cantidad_Max'), DB::raw('SUM(Stock) as Total_Stock'))
            ->where('Id_Maquina', $this->idMaquina)
            ->groupBy('Id_Articulo', 'Id_Maquina')
            ->get();

        // Obtener las descripciones de los artículos
        $articulos = DB::table('Cat_Articulos')
            ->pluck('Txt_Descripcion', 'Id_Articulo')
            ->toArray();

        // Obtener los detalles de la máquina
        $detallesMaquina = DB::table('Ctrl_Mquinas as cm')
            ->leftJoin('Stat_Mquinas as sm', 'cm.Id_Maquina', '=', 'sm.Id_Maquina')
            ->leftJoin('Cat_Dispositivo as cd', 'cm.Id_Dispositivo', '=', 'cd.Id_Dispositivo')
            ->select(
                'cm.Txt_Nombre',
                'cm.Txt_Serie_Maquina',
                'cm.Txt_Tipo_Maquina',
                'cm.Txt_Estatus as Estatus_Maquina',
                'cm.Capacidad',
                'sm.Per_Alm as Almacenamiento',
                'cd.Txt_Serie_Dispositivo',
                'cd.Txt_Estatus as Estatus_Dispositivo'
            )
            ->where('cm.Id_Maquina', $this->idMaquina)
            ->first();

        // Formatear los datos de configuración
        $data = $configuracion->map(function ($item) use ($articulos) {
            return [
                'Articulo' => $articulos[$item->Id_Articulo] ?? 'Desconocido',
                'Total_Stock' => $item->Total_Stock,
                'Total_Cantidad_Max' => $item->Total_Cantidad_Max,
            ];
        });
        

        // Agregar los detalles de la máquina al principio
        $detalles = collect([
            ['Nombre de la máquina', $detallesMaquina->Txt_Nombre],
            ['Serie de la máquina', $detallesMaquina->Txt_Serie_Maquina],
            ['Tipo de máquina', $detallesMaquina->Txt_Tipo_Maquina],
            ['Capacidad', $detallesMaquina->Capacidad],
            ['Almacenamiento (%)', $detallesMaquina->Almacenamiento],
            ['Serie del dispositivo', $detallesMaquina->Txt_Serie_Dispositivo],
            ['Estatus del dispositivo', $detallesMaquina->Estatus_Dispositivo],
        ]);

        // Encabezado de datos de configuración
        $encabezadoConfiguracion = collect([['Artículo', 'Existencias', 'Capacidad Máxima','Rellenar']]);

        // Combinar datos y añadir espacio antes del encabezado de la segunda consulta
        return $detalles->concat(collect([['']])) // Espacio antes del encabezado de la segunda consulta
                        ->concat($encabezadoConfiguracion)
                        ->concat($data);
                        
    }

    public function headings(): array
    {
        return [
            'Descripción',
            'Valor',
        ];
    }

    public function title(): string
    {
        return $this->nombreMaquina;
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
                $sheet->setCellValue('A1', 'Reporte de Inventario Vending Machine');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Mover encabezado a la fila 2
                $sheet->insertNewRowBefore(2, 1); // Insertar una fila en la fila 2
                $sheet->fromArray($this->headings(), NULL, 'A2'); // Agregar encabezados en la fila 2

                // Encabezado en negritas
                $sheet->getStyle('A2:B2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A11:D11')->getFont()->setBold(true)->setSize(12);

                

                 // Obtener la cantidad de registros dinámicamente
            $totalRows = count($this->collection()) + 3; // +2 por los encabezados y el espacio

            // Aplicar la fórmula desde la fila correspondiente hasta el total de filas
            for ($row = 12; $row < $totalRows; $row++) {
                $sheet->setCellValue('D' . $row, "=C$row-B$row");
            }

            // Ajustar otras configuraciones, encabezados, estilos, etc.
            $highestRow = $sheet->getHighestRow();
            $sheet->insertNewRowBefore($highestRow + 1, 1);

                // Espacio antes del encabezado de artículos
                $highestRow = $sheet->getHighestRow();
                $sheet->insertNewRowBefore($highestRow + 1, 1);

                // Negritas en la fila de Artículo, Existencias, Capacidad Máxima
                $sheet->getStyle('A' . ($highestRow + 2) . ':C' . ($highestRow + 2))->getFont()->setBold(true);

                // Ajustar columnas automáticamente
                foreach (range('A', 'H') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Mensaje en el pie de página
                $sheet->mergeCells('A' . ($highestRow + 5) . ':H' . ($highestRow + 5));
                $sheet->setCellValue('A' . ($highestRow + 5), 'Este formato no es un CSV, por lo que no es válido para subir a portal. Siga su manual de Usuario.');
                $sheet->getStyle('A' . ($highestRow + 5))->getFont()->getColor()->setRGB('FF0000');
                $sheet->getStyle('A' . ($highestRow + 5))->getFont()->setSize(10);
            },
        ];
    }
}