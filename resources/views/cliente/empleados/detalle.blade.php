@extends('adminlte::page')

@section('title', __('Detalle de Empleado'))

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <h1>
                    <a href="{{ route('empleados-cli') }}" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fas fa-arrow-left"></i> Volver a Empleados
                    </a>
                    Detalle de Consumo
                </h1>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    <!-- Profile Card -->
    <div class="card card-widget widget-user">
        <div class="widget-user-header bg-info">
            <h3 class="widget-user-username">{{ $empleado->NombreCompleto }}</h3>
            <h5 class="widget-user-desc">{{ $empleado->Area }}</h5>
        </div>
        <div class="widget-user-image">
            <img class="img-circle elevation-2" style="background: white; object-fit: contain;"
                src="/Images/industrial-user.png" onerror="this.onerror=null;this.src='/Images/user.png';"
                alt="User Avatar">
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">{{ $empleado->No_Empleado }}</h5>
                        <span class="description-text">NÚMERO DE EMPLEADO</span>
                    </div>
                </div>
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">{{ $empleado->Nip ?? 'N/A' }}</h5>
                        <span class="description-text">NIP</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="description-block">
                        <h5 class="description-header">{{ $empleado->Txt_Estatus }}</h5>
                        <span class="description-text">ESTATUS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Stats -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros de Reporte</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Inputs para el rango de fechas -->
                        <div class="col-md-3">
                            <label for="startDate">Fecha Inicio:</label>
                            <input type="text" id="startDate" class="form-control" placeholder="AAAA-MM-DD">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">Fecha Fin:</label>
                            <input type="text" id="endDate" class="form-control" placeholder="AAAA-MM-DD">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button class="btn btn-primary btn-block" id="btnFilter">Filtrar</button>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-success btn-block" id="btnExport">Exportar a Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Consumo por Producto</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="consumptionChart"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Tendencia de Consumo</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="lineChart"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Historial de Consumos Detallado</h3>
                </div>
                <div class="card-body">
                    <table id="consumptionReport" class="table table-striped table-bordered dt-responsive nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Imagen</th>
                                <th>Área</th>
                                <th>Producto</th>
                                <th>Código Urvina</th>
                                <th>Código Cliente</th>
                                <th>Fecha</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function renderImagen(data, type, row) {
        if (!data) return '<span class="text-muted" style="font-size:10px;">Sin imagen</span>';
        return `<img src="/Images/Catalogo/${data}.jpg" alt="${data}" style="width: 40px; height: 40px; object-fit: contain;" onerror="this.onerror=null;this.src='/Images/product.png';">`;
    }

    $(document).ready(function () {
        // Datepickers
        $("#startDate, #endDate").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onSelect: function () {
                table.draw();
            }
        });

        $('#btnExport').click(function () {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var url = '{{ route("empleados.detalle.export", ["id" => $empleado->Id_Empleado]) }}';

            // Build query parameters
            var params = [];
            if (startDate) params.push('startDate=' + encodeURIComponent(startDate));
            if (endDate) params.push('endDate=' + encodeURIComponent(endDate));

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.location.href = url;
        });

        // DataTable
        var table = $('#consumptionReport').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("empleados.consumos.data", ["id" => $empleado->Id_Empleado]) }}',
                data: function (d) {
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                }
            },
            columns: [
                { data: 'Numero_de_empleado', title: 'No. Empleado' },
                { data: 'Codigo_Urvina', render: renderImagen, orderable: false, searchable: false, title: 'Cant.' },
                { data: 'Area' },
                { data: 'Producto' },
                { data: 'Codigo_Urvina' },
                { data: 'Codigo_Cliente' },
                { data: 'Fecha' },
                { data: 'Cantidad' }
            ],
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json"
            }
        });

        // CHARTS CONFIGURATION

        // 1. Consumption by Product (Donut or Bar)
        var ctxProduct = document.getElementById('consumptionChart').getContext('2d');
        var productChart = new Chart(ctxProduct, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'left'
                }
            }
        });

        // 2. Trend Chart (Now Bar for better visibility)
        var ctxLine = document.getElementById('lineChart').getContext('2d');
        var lineChart = new Chart(ctxLine, {
            type: 'bar', // Changed to Bar
            data: {
                labels: [],
                datasets: [{
                    label: 'Consumo Diario',
                    data: [],
                    borderColor: '#3c8dbc',
                    backgroundColor: 'rgba(60, 141, 188, 0.7)', // Increased opacity
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { display: true },
                    y: { beginAtZero: true }
                }
            }
        });

        // Update Charts on Table Draw (using full dataset from server)
        table.on('xhr', function () {
            var json = table.ajax.json();
            if (!json || !json.chartProductData || !json.chartTrendData) return;

            // --- Update Product Chart ---
            var labelsProd = [];
            var dataProd = [];

            json.chartProductData.forEach(function (item) {
                labelsProd.push(item.Producto);
                dataProd.push(item.Total);
            });

            productChart.data.labels = labelsProd;
            productChart.data.datasets[0].data = dataProd;
            productChart.update();

            // --- Update Line Chart ---
            var labelsTrend = [];
            var dataTrend = [];

            json.chartTrendData.forEach(function (item) {
                labelsTrend.push(item.Fecha);
                dataTrend.push(item.Total);
            });

            lineChart.data.labels = labelsTrend;
            lineChart.data.datasets[0].data = dataTrend;
            lineChart.update();
        });

    });
</script>
@stop