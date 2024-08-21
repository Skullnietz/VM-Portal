@extends('adminlte::page')

@section('title', __('Reporte Consumo'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Reporte de Consumo por Empleado') }}
            </h4>
        </div>
        <div class="col text-right">
        <form id="export-form" action="{{ route('export.consumoxempleado') }}" method="GET">
    <!-- Campos ocultos que se actualizarán -->
    <input type="hidden" name="area" id="filter-area" value="{{ request()->input('area') }}">
    <input type="hidden" name="product" id="filter-product" value="{{ request()->input('product') }}">
    <input type="hidden" name="employee" id="filter-employee" value="{{ request()->input('employee') }}">
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
            <div class="col-md-2">
                <label for="filterArea">Área:</label>
                <input type="text" id="filterArea" class="form-control" placeholder="Filtrar por Área">
            </div>
            <div class="col-md-3">
                <label for="filterProduct">Producto:</label>
                <input type="text" id="filterProduct" class="form-control" placeholder="Filtrar por Producto">
            </div>
            <div class="col-md-3">
                <label for="filterEmployee">Empleado:</label>
                <input type="text" id="filterEmployee" class="form-control" placeholder="Filtrar por Empleado">
            </div>
                </div><br>
                
            <div class="row">
            <div class="col">
                <table id="consumptionReport" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Número de empleado</th>
                            <th>Área</th>
                            <th>Producto</th>
                            <th>Código Urvina</th>
                            <th>Código Cliente</th>
                            <th>Fecha</th>
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
                        <a class="nav-link active" id="employee-tab" data-toggle="tab" href="#employeeChart" role="tab" aria-controls="employeeChart" aria-selected="false">Gráfica de Consumo por Empleado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="chart-tab" data-toggle="tab" href="#chartContent" role="tab" aria-controls="chartContent" aria-selected="true">Tabla de Consumos de Articulo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="chartline-tab" data-toggle="tab" href="#chartlineContent" role="tab" aria-controls="chartlineContent" aria-selected="false">Gráfica de Consumo por Fecha</a>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="employeeChart" role="tabpanel" aria-labelledby="employee-tab">
                        <canvas id="employeeConsumptionChart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="chartContent" role="tabpanel" aria-labelledby="chart-tab">
                        <canvas id="consumptionChart"></canvas>
                    </div>
                    <div class="tab-pane fade" id="chartlineContent" role="tabpanel" aria-labelledby="chartline-tab">
                        <canvas id="lineChart"></canvas>
                    </div>
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
$(document).ready(function() {
    $("#startDate").datepicker({
        dateFormat: 'dd/mm/yy',
        firstDay: 1, // Inicia la semana en lunes
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
    });

    $("#endDate").datepicker({
        dateFormat: 'dd/mm/yy',
        firstDay: 1, // Inicia la semana en lunes
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario y de los inputs
    const exportForm = document.getElementById('export-form');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const filterAreaInput = document.getElementById('filterArea');
    const filterProductInput = document.getElementById('filterProduct');
    const filterEmployeeInput = document.getElementById('filterEmployee');

    // Función para actualizar los campos ocultos
    function updateHiddenFields() {
        document.getElementById('filter-area').value = filterAreaInput.value;
        document.getElementById('filter-product').value = filterProductInput.value;
        document.getElementById('filter-employee').value = filterEmployeeInput.value;
        document.getElementById('filter-dateRange').value = startDateInput.value + ' - ' + endDateInput.value;
    }

    // Evento de clic en el botón de exportación
    exportForm.addEventListener('submit', function(event) {
        // Actualiza los campos ocultos antes de enviar el formulario
        updateHiddenFields();
    });
});
</script>
<script>
    // Inicializar Datepicker para ambas fechas
    $('#startDate').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            table.draw();
        }
    });

    $('#endDate').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            table.draw();
        }
    });

    // Inicializar DataTable
    var table = $('#consumptionReport').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ url("/getconsumoxempleado/data") }}', // Reemplaza con la ruta a tu controlador
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.area = $('#filterArea').val();
                d.product = $('#filterProduct').val();
                d.employee = $('#filterEmployee').val();
            },
            complete: function (response) {
                console.log('API Response:', response);
            }
        },
        columns: [
            { data: 'Nombre' },
            { data: 'Numero_de_empleado' },
            { data: 'Area' },
            { data: 'Producto' },
            { data: 'Codigo_Urvina' },
            { data: 'Codigo_Cliente' },
            { data: 'Fecha' }
        ],
            responsive: true,
            scrollX: true,
        language: {
            processing: "Procesando...",
            search: "Buscar en todos los campos:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    });

    // Filtros personalizados
    $('#filterArea, #filterProduct, #filterEmployee').on('keyup', function() {
        table.draw();
    });

    // Inicializar Chart.js
    var ctx = document.getElementById('consumptionChart').getContext('2d');
    var consumptionChart = new Chart(ctx, {
        type: 'bar', // Puedes cambiar esto a 'line', 'pie', etc.
        data: {
            labels: [], // Etiquetas vacías, se llenarán dinámicamente
            datasets: [{
                label: 'Cantidad de Articulos Consumidos',
                data: [], // Datos vacíos, se llenarán dinámicamente
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Actualizar la gráfica cuando la tabla se dibuja o se filtra
    table.on('draw', function() {
        var data = table.rows({ filter: 'applied' }).data();

        // Limpiar datos previos
        consumptionChart.data.labels = [];
        consumptionChart.data.datasets[0].data = [];

        // Recorre los datos y agrégalos a la gráfica
        data.each(function(row) {
            var producto = row.Producto; // Nombre del producto
            var cantidad = row.Cantidad; // Cantidad consumida (asegúrate de que el campo exista)
            
            // Si el producto ya está en las etiquetas, suma la cantidad
            var labelIndex = consumptionChart.data.labels.indexOf(producto);
            if (labelIndex >= 0) {
                consumptionChart.data.datasets[0].data[labelIndex] += parseInt(cantidad);
            } else {
                // Si no está, agrégalo como nuevo
                consumptionChart.data.labels.push(producto);
                consumptionChart.data.datasets[0].data.push(parseInt(cantidad));
            }
        });

        // Actualiza la gráfica
        consumptionChart.update();
    });

    // Inicializar segunda gráfica (Productos consumidos por empleado)
var ctx2 = document.getElementById('employeeConsumptionChart').getContext('2d');
var employeeConsumptionChart = new Chart(ctx2, {
    type: 'bar', // Puedes cambiar esto a 'line', 'pie', etc.
    data: {
        labels: [], // Etiquetas vacías, se llenarán dinámicamente
        datasets: [{
            label: 'Cantidad de Productos Consumidos por Empleado',
            data: [], // Datos vacíos, se llenarán dinámicamente
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Actualizar la segunda gráfica cuando la tabla se dibuja o se filtra
table.on('draw', function() {
    var data = table.rows({ filter: 'applied' }).data();

    // Limpiar datos previos
    employeeConsumptionChart.data.labels = [];
    employeeConsumptionChart.data.datasets[0].data = [];

    // Recorre los datos y agrégalos a la segunda gráfica
    data.each(function(row) {
        var empleado = row.Nombre; // Nombre del empleado
        var cantidad = row.Cantidad; // Cantidad consumida (asegúrate de que el campo exista)
        
        // Si el empleado ya está en las etiquetas, suma la cantidad
        var labelIndex = employeeConsumptionChart.data.labels.indexOf(empleado);
        if (labelIndex >= 0) {
            employeeConsumptionChart.data.datasets[0].data[labelIndex] += parseInt(cantidad);
        } else {
            // Si no está, agrégalo como nuevo
            employeeConsumptionChart.data.labels.push(empleado);
            employeeConsumptionChart.data.datasets[0].data.push(parseInt(cantidad));
        }
    });

    // Actualiza la segunda gráfica
    employeeConsumptionChart.update();
});


  // Inicializa el gráfico de línea
  var ctxLine = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: [], // Fechas
            datasets: [{
                label: 'Consumo',
                data: [], // Datos de consumo
                borderColor: 'rgba(104, 255, 81, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Fecha'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Consumo'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Función para actualizar el gráfico de línea
    function updateChart() {
        var data = table.rows({ filter: 'applied' }).data().toArray();
        var dateMap = {};

        // Agrupa los datos por fecha
        data.forEach(function(row) {
            var fecha = row.Fecha; // Asegúrate de que la fecha esté en el formato adecuado
            var cantidad = row.Cantidad; // Usa el nombre de la columna adecuado

            if (dateMap[fecha]) {
                dateMap[fecha] += parseInt(cantidad);
            } else {
                dateMap[fecha] = parseInt(cantidad);
            }
        });

        // Ordena las fechas y datos para el gráfico
        var sortedDates = Object.keys(dateMap).sort();
        var sortedConsumption = sortedDates.map(date => dateMap[date]);

        // Actualiza el gráfico con los datos agrupados
        lineChart.data.labels = sortedDates;
        lineChart.data.datasets[0].data = sortedConsumption;
        lineChart.update();
    }


    // Actualiza el gráfico cuando la tabla se dibuja o se filtra
    table.on('draw', function() {
        updateChart();
    });

    // Inicializar el gráfico al cargar la página
    updateChart();



    // Función para regresar
    function goBack() {
        window.history.back();
    }
</script>

@stop