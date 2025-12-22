@extends('adminlte::page')

@section('title', __('Reporte Consumo por Empleado - Admin'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                        class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Reporte de Consumo por Empleado (Admin)') }}
            </h4>
        </div>
        <div class="col text-right">
            <!-- Export form placeholder - can be implemented later -->
            <!--
            <form id="export-form" action="{{ route('export.consumoxempleado') }}" method="GET">
                <input type="hidden" name="area[]" id="filter-area" value="{{ request()->input('area') }}">
                <input type="hidden" name="product[]" id="filter-product" value="{{ request()->input('product') }}">
                <input type="hidden" name="employee" id="filter-employee" value="{{ request()->input('employee') }}">
                <input type="hidden" name="dateRange" id="filter-dateRange" value="{{ request()->input('dateRange') }}">
                <input type="hidden" name="planta_id" value="{{ request()->input('planta_id') }}">

                <button type="submit" class="btn btn-success">Exportar a Excel&nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i></button>
            </form>
            -->
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="card w-100">
            <div class="card-header">
                <h5 class="card-title">Filtros</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.consumoxempleado.index') }}" id="plant-form">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="planta_id">Planta:</label>
                            <select id="planta_id" name="planta_id" class="form-control select2"
                                onchange="this.form.submit()">
                                <option value="">Seleccione una planta</option>
                                @foreach($plantas as $planta)
                                    <option value="{{ $planta->Id_Planta }}" {{ request('planta_id') == $planta->Id_Planta ? 'selected' : '' }}>
                                        {{ $planta->Nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                @if(request('planta_id'))
                    <hr>
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
                            <select id="filterArea" name="area[]" class="form-control select2" multiple>
                                <option value="">Seleccione un área</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->Txt_Nombre }}">{{ $area->Txt_Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterProduct">Producto:</label>
                            <select id="filterProduct" name="producto[]" class="form-control select2" multiple>
                                <option value="">Seleccione un producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->Txt_Descripcion }}">{{ $producto->Txt_Descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterEmployee">Empleado:</label>
                            <select id="filterEmployee" name="employee[]" class="form-control select2" multiple>
                                <option value="">Seleccione empleados</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado->Nombre}} {{ $empleado->APaterno}} {{ $empleado->AMaterno}}">
                                        {{ $empleado->Nombre}} {{ $empleado->APaterno}} {{ $empleado->AMaterno}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(request('planta_id'))
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tabla de Consumos por Empleado</h5>
                    </div>
                    <div class="card-body">
                        <table id="consumptionReport" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Imagen</th>
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

        <!-- Gráficas -->
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
                                <a class="nav-link active" id="employee-tab" data-toggle="tab" href="#employeeChart"
                                    role="tab" aria-controls="employeeChart" aria-selected="false">Gráfica de Consumo por
                                    Empleado</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="chart-tab" data-toggle="tab" href="#chartContent" role="tab"
                                    aria-controls="chartContent" aria-selected="true">Tabla de Consumos de Articulo</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="chartline-tab" data-toggle="tab" href="#chartlineContent" role="tab"
                                    aria-controls="chartlineContent" aria-selected="false">Gráfica de Consumo por Fecha</a>
                            </li>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="employeeChart" role="tabpanel"
                                aria-labelledby="employee-tab">
                                <canvas id="employeeConsumptionChart"></canvas>
                            </div>
                            <div class="tab-pane fade" id="chartContent" role="tabpanel" aria-labelledby="chart-tab">
                                <canvas id="consumptionChart"></canvas>
                            </div>
                            <div class="tab-pane fade" id="chartlineContent" role="tabpanel"
                                aria-labelledby="chartline-tab">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            Por favor, seleccione una planta para ver el reporte.
        </div>
    @endif
</div>
@stop

@section('css')
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function renderImagen(data, type, row) {
        if (!data) return '<span class="text-muted" style="font-size:10px;">Sin imagen</span>';
        return `<img src="/Images/Catalogo/${data}.jpg" alt="${data}" style="width: 50px; height: 50px; object-fit: contain;" onerror="this.onerror=null;this.src='/Images/product.png';">`;
    }

    $(document).ready(function () {
        $('.select2').select2({
            width: '100%'
        });

        @if(request('planta_id'))

            // Datepicker initialization
            $("#startDate, #endDate").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                onSelect: function () {
                    table.draw();
                }
            });

            // DataTable Initialization
            var table = $('#consumptionReport').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.getconsumoxempleado.data") }}',
                    data: function (d) {
                        d.planta_id = '{{ request("planta_id") }}';
                        d.startDate = $('#startDate').val();
                        d.endDate = $('#endDate').val();
                        d.area = $('#filterArea').val();
                        d.product = $('#filterProduct').val();
                        d.employee = $('#filterEmployee').val();
                    }
                },
                lengthMenu: [[100, 500, 1000, -1], [100, 500, 1000, "Todos"]],
                pageLength: 100,
                columns: [
                    { data: 'Nombre' },
                    { data: 'Codigo_Urvina', render: renderImagen, orderable: false, searchable: false },
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
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                }
            });

            // Refresh table on filter change
            $('#filterArea, #filterProduct, #filterEmployee').on('change', function () {
                table.draw();
            });

            // Charts Initialization (Placeholder logic similar to original)
            var ctx = document.getElementById('consumptionChart').getContext('2d');
            var consumptionChart = new Chart(ctx, {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Cantidad de Articulos Consumidos', data: [], backgroundColor: 'rgba(75, 192, 192, 0.2)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 }] },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            var ctx2 = document.getElementById('employeeConsumptionChart').getContext('2d');
            var employeeConsumptionChart = new Chart(ctx2, {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Cantidad de Productos Consumidos por Empleado', data: [], backgroundColor: 'rgba(153, 102, 255, 0.2)', borderColor: 'rgba(153, 102, 255, 1)', borderWidth: 1 }] },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            var ctxLine = document.getElementById('lineChart').getContext('2d');
            var lineChart = new Chart(ctxLine, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Consumo', data: [], borderColor: 'rgba(104, 255, 81, 1)', backgroundColor: 'rgba(75, 192, 192, 0.2)', fill: true }] },
                options: { responsive: true, scales: { x: { title: { display: true, text: 'Fecha' } }, y: { title: { display: true, text: 'Consumo' }, beginAtZero: true } } }
            });

            // Update charts on table draw
            table.on('draw', function () {
                var data = table.rows({ filter: 'applied' }).data();

                // Update Consumption Chart
                var prodMap = {};
                data.each(function (row) {
                    var prod = row.Producto;
                    var qty = parseInt(row.Cantidad || 1); // Assuming 1 if not present, or fetch from row
                    prodMap[prod] = (prodMap[prod] || 0) + qty;
                });
                consumptionChart.data.labels = Object.keys(prodMap);
                consumptionChart.data.datasets[0].data = Object.values(prodMap);
                consumptionChart.update();

                // Update Employee Chart
                var empMap = {};
                data.each(function (row) {
                    var emp = row.Nombre;
                    var qty = parseInt(row.Cantidad || 1);
                    empMap[emp] = (empMap[emp] || 0) + qty;
                });
                employeeConsumptionChart.data.labels = Object.keys(empMap);
                employeeConsumptionChart.data.datasets[0].data = Object.values(empMap);
                employeeConsumptionChart.update();

                // Update Line Chart
                var dateMap = {};
                data.each(function (row) {
                    var date = row.Fecha.split(' ')[0]; // Simple date extraction
                    var qty = parseInt(row.Cantidad || 1);
                    dateMap[date] = (dateMap[date] || 0) + qty;
                });
                var sortedDates = Object.keys(dateMap).sort();
                lineChart.data.labels = sortedDates;
                lineChart.data.datasets[0].data = sortedDates.map(d => dateMap[d]);
                lineChart.update();
            });
        @endif
        });

    function goBack() {
        window.history.back();
    }
</script>
@stop