@extends('adminlte::page')

@section('title', __('Reporte Consumo por Vending'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Reporte de Consumo por VM') }}
            </h4>
        </div>
        <div class="col text-right">
        <form id="export-form" action="{{ route('export.consumoxvending') }}" method="GET">
    <!-- Campos ocultos que se actualizarán -->
    <input type="hidden" name="vending" id="filter-vending" value="{{ request()->input('vending') }}">
    <input type="hidden" name="area" id="filter-area" value="{{ request()->input('area') }}">
    <input type="hidden" name="product" id="filter-product" value="{{ request()->input('product') }}">
    <input type="hidden" name="dateRange" id="filter-dateRange" value="{{ request()->input('dateRange') }}">

    <button type="submit" class="btn btn-success">Exportar a Excel&nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i></button>
</form>
        </div>
    </div>
</div>
    
    
@stop

@section('content')
    <div class="container">
    <div class="row mb-2">
        
        <div class="card">
        <div class="card-header">
                        <h5 class="card-title">Tabla de Consumos por Vending </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
            <div class="card-body">
                <div class="row">
                    <!-- Inputs para el rango de fechas -->
            <div class="col-md-2">
                <label for="startDate">Fecha Inicio:</label>
                <input type="text" id="startDate" class="form-control" placeholder="AAAA-MM-DD">
            </div>
            <div class="col-md-2">
                <label for="endDate">Fecha Fin:</label>
                <input type="text" id="endDate" class="form-control" placeholder="AAAA-MM-DD">
            </div>
            <div class="col-md-3">
                <label for="filterArea">Área:</label>
                <input type="text" id="filterArea" class="form-control" placeholder="Filtrar por Área">
            </div>
            <div class="col-md-2">
                <label for="filterProduct">Producto:</label>
                <input type="text" id="filterProduct" class="form-control" placeholder="Filtrar por Producto">
            </div>
            <div class="col-md-3">
                <label for="filterVM">Vending Machine:</label>
                <input type="text" id="filterVM" class="form-control" placeholder="Filtrar por VM">
            </div>
                </div><br>
                
            <div class="row">
            <div class="col">
                <table id="consumptionReport" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Vending</th>
                            <th>Total Consumo</th>
                            <th>No.Empleados</th>
                            <th>Área</th>
                            <th>Producto</th>
                            <th>Código Urvina</th>
                            <th>Código Cliente</th>
                            <th>Utlimo Consumo</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

            </div>
        </div>
        <!-- Nuevo Card para las Gráficas -->
    <div class="row mt-4">
        <div class="col">
        <div class="card">
                <div class="card-header">
                <h5 class="card-title">Gráficas de Consumo</h5>
                </div>
            
                <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="consumptionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="employee-tab" data-toggle="tab" href="#employeeChart" role="tab" aria-controls="employeeChart" aria-selected="false">Gráfica de Consumo por Vending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="chart-tab" data-toggle="tab" href="#chartContent" role="tab" aria-controls="chartContent" aria-selected="true">Grafica de Consumo de Vending por Area</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="chartline-tab" data-toggle="tab" href="#chartlineContent" role="tab" aria-controls="chartlineContent" aria-selected="false">Grafica de Consumo de Vending por Area y Producto</a>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="employeeChart" role="tabpanel" aria-labelledby="employee-tab">
                    <canvas id="machineConsumptionChart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="chartContent" role="tabpanel" aria-labelledby="chart-tab">
                    <canvas id="areaVendingChart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="chartlineContent" role="tabpanel" aria-labelledby="chartline-tab">
                    <canvas id="productAreaMachineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
            
        </div>
        
@stop

@section('css')
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <style>
/* Estilo para celdas con texto largo */
.dataTables_wrapper .dataTables_scroll .dataTables_scrollBody .dataTables_scroll .dataTables_scrollBody .dataTables_scroll .dataTables_scrollBody td {
    max-width: 200px; /* Ajusta el tamaño máximo según sea necesario */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    position: relative;
}

/* Estilo para mostrar el texto completo al pasar el ratón */
.tooltip {
    position: absolute;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 10;
    max-width: 300px; /* Ajusta el tamaño máximo según sea necesario */
    white-space: normal; /* Permite el salto de línea */
}

.dataTables_wrapper .dataTables_scroll .dataTables_scrollBody td:hover .tooltip {
    visibility: visible;
    opacity: 1;
}
.dataTables_filter {
    display: none;
}

    </style>
@stop

@section('js')
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario y de los inputs
    const exportForm = document.getElementById('export-form');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const filterAreaInput = document.getElementById('filterArea');
    const filterProductInput = document.getElementById('filterProduct');
    const filterVendingMachine = document.getElementById('filterVM');

    // Función para actualizar los campos ocultos
    function updateHiddenFields() {
        document.getElementById('filter-area').value = filterAreaInput.value;
        document.getElementById('filter-product').value = filterProductInput.value;
        document.getElementById('filter-dateRange').value = startDateInput.value + ' - ' + endDateInput.value;
        document.getElementById('filter-vending').value = filterVendingMachine.value;
    }

    // Evento de clic en el botón de exportación
    exportForm.addEventListener('submit', function(event) {
        // Actualiza los campos ocultos antes de enviar el formulario
        updateHiddenFields();
    });
});
</script>
<script>
  $(document).ready(function() {
    var dateFormat = "yy-mm-dd";

    // Inicializar el datepicker para el campo de fecha de inicio
    $("#startDate").datepicker({
        dateFormat: dateFormat,
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(selectedDate) {
            var minDate = $(this).datepicker("getDate");
            $("#endDate").datepicker("option", "minDate", minDate);
            // Redibujar la tabla al cambiar la fecha
            $('#consumptionReport').DataTable().draw();
        }
    });

    // Inicializar el datepicker para el campo de fecha de fin
    $("#endDate").datepicker({
        dateFormat: dateFormat,
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(selectedDate) {
            var maxDate = $(this).datepicker("getDate");
            $("#startDate").datepicker("option", "maxDate", maxDate);
            // Redibujar la tabla al cambiar la fecha
            $('#consumptionReport').DataTable().draw();
        }
    });

    // Inicialización de DataTables
    var table = $('#consumptionReport').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ url("/getconsumoxvending/data") }}',
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.area = $('#filterArea').val();
                d.product = $('#filterProduct').val();
                d.vending = $('#filterVM').val();
            }
        },
        columns: [
            { data: 'Maquina', title: 'Vending' },
            { data: 'Total_Consumos', title: 'Total Consumo' },
            { 
                data: 'No_Empleados', 
                title: 'No. Empleados', 
                render: function(data, type, row) {
                    var nombresEmpleados = row.Nombres_Empleados || '';
                    return '<span title="' + nombresEmpleados + '">' + data + '</span>';
                }
            },
            { data: 'Area', title: 'Area' },
            { data: 'Producto', title: 'Producto' },
            { data: 'Codigo_Urvina', title: 'Código Urvina' },
            { data: 'Codigo_Cliente', title: 'Código Cliente' },
            { data: 'Ultimo_Consumo', title: 'Último Consumo' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
        }
    });

    // Manejar la acción de keyup en los campos de filtro
    $('#filterArea, #filterProduct, #filterVM, #startDate, #endDate').on('keyup change', function() {
        table.ajax.reload();
    });

     // Gráfica de doughnut para máquinas con mayor consumo de productos
    var ctxDoughnutMachine = document.getElementById('machineConsumptionChart').getContext('2d');
    var machineConsumptionChart = new Chart(ctxDoughnutMachine, {
        type: 'doughnut',
        data: {
            labels: [], // Se llenará con nombres de máquinas
            datasets: [{
                label: 'Consumo por máquina',
                data: [], // Aquí va el total de consumos por máquina
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#7C4DFF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Controlar tamaño manualmente
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                }
            }
        }
    });

    // Gráfica de polar area para consumo de vending por área y máquina
    var ctxPolarArea = document.getElementById('areaVendingChart').getContext('2d');
    var areaVendingChart = new Chart(ctxPolarArea, {
        type: 'polarArea',
        data: {
            labels: [], // Etiquetas combinadas de área y máquina
            datasets: [{
                label: 'Consumo por área y máquina',
                data: [], // Total de consumo por combinación de área y máquina
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                }
            }
        }
    });

    // Gráfica de barras para productos consumidos en áreas y máquinas
    var ctxBar = document.getElementById('productAreaMachineChart').getContext('2d');
    var productAreaMachineChart = new Chart(ctxBar, {
        type: 'pie',
        data: {
            labels: [], // Etiquetas que serán combinaciones de Área - Producto - Máquina
            datasets: [{
                label: 'Consumo total',
                data: [], // Cantidad total consumida en cada combinación
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                }
            }
        }
    });

    // Función para actualizar las gráficas
function updateAllCharts() {
    var data = table.rows({ filter: 'applied' }).data().toArray();

    // Limpiar datos anteriores
    machineConsumptionChart.data.labels = [];
    machineConsumptionChart.data.datasets[0].data = [];
    areaVendingChart.data.labels = [];
    areaVendingChart.data.datasets[0].data = [];
    productAreaMachineChart.data.labels = [];
    productAreaMachineChart.data.datasets[0].data = [];

    var machineConsumptionMap = {}; // Mapa para la gráfica de máquinas con mayor consumo
    var areaVendingMap = {};        // Mapa para la gráfica de áreas con mayor consumo (incluyendo máquina)
    var productAreaMachineMap = {}; // Mapa para la gráfica combinada de producto, área y máquina

    // Recorrer los datos y actualizar las gráficas
    data.forEach(function(row) {
        var maquina = row.Maquina; // Nombre de la máquina
        var area = row.Area; // Nombre del área
        var producto = row.Producto; // Producto consumido
        var cantidad = parseInt(row.Total_Consumos); // Cantidad consumida

        // Gráfico de consumo por máquina (Doughnut chart)
        if (machineConsumptionMap[maquina]) {
            machineConsumptionMap[maquina] += cantidad;
        } else {
            machineConsumptionMap[maquina] = cantidad;
        }

        // Gráfico de consumo por área y máquina (Polar area chart)
        var areaMachineLabel = area + ' - ' + maquina; // Etiqueta combinada de área y máquina
        if (areaVendingMap[areaMachineLabel]) {
            areaVendingMap[areaMachineLabel] += cantidad;
        } else {
            areaVendingMap[areaMachineLabel] = cantidad;
        }

        // Gráfico combinado (Bar chart) - Combinación de área, producto y máquina
        var productAreaMachineLabel = area + ' - ' + producto + ' - ' + maquina;
        if (productAreaMachineMap[productAreaMachineLabel]) {
            productAreaMachineMap[productAreaMachineLabel] += cantidad;
        } else {
            productAreaMachineMap[productAreaMachineLabel] = cantidad;
        }
    });

    // Llenar los datos en el gráfico de consumo por máquina
    Object.keys(machineConsumptionMap).forEach(function(maquina) {
        machineConsumptionChart.data.labels.push(maquina); // Nombre de las máquinas
        machineConsumptionChart.data.datasets[0].data.push(machineConsumptionMap[maquina]); // Total de consumo por máquina
    });

    // Llenar los datos en el gráfico de consumo por área y máquina
    Object.keys(areaVendingMap).forEach(function(areaMachineLabel) {
        areaVendingChart.data.labels.push(areaMachineLabel); // Etiqueta combinada de área y máquina
        areaVendingChart.data.datasets[0].data.push(areaVendingMap[areaMachineLabel]); // Total de consumo por área y máquina
    });

    // Llenar los datos en el gráfico combinado de producto, área y máquina
    Object.keys(productAreaMachineMap).forEach(function(label) {
        productAreaMachineChart.data.labels.push(label); // Combinación de área - producto - máquina
        productAreaMachineChart.data.datasets[0].data.push(productAreaMachineMap[label]); // Total de consumo
    });

    // Actualizar las gráficas
    machineConsumptionChart.update();
    areaVendingChart.update();
    productAreaMachineChart.update();
}

    // Actualizar las gráficas cuando la tabla se dibuje o filtre
    table.on('draw', function() {
        updateAllCharts();
    });

    // Actualizar las gráficas al cargar la página
    updateAllCharts();

});

</script>


@stop
