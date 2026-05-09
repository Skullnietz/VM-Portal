@extends('adminlte::page')

@section('title', 'Tendencias de Consumo')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Tendencias de Consumo
            </h4>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container">
    {{-- Filtros --}}
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="card-title">Filtros</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="planta_id">Planta:</label>
                    <select id="planta_id" class="form-control select2">
                        <option value="">Todas las plantas</option>
                        @foreach($plantas as $planta)
                            <option value="{{ $planta->Id_Planta }}">{{ $planta->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="maquina_id">Máquina:</label>
                    <select id="maquina_id" class="form-control select2" disabled>
                        <option value="">Todas las máquinas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="meses">Periodo:</label>
                    <select id="meses" class="form-control">
                        <option value="3">Últimos 3 meses</option>
                        <option value="6" selected>Últimos 6 meses</option>
                        <option value="12">Últimos 12 meses</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="btnConsultar" class="btn btn-primary btn-block">
                        <i class="fas fa-chart-line"></i> Generar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfica de líneas --}}
    <div class="card mb-3" id="chartCard" style="display:none;">
        <div class="card-header">
            <h5 class="card-title">Consumo por Artículo en el Tiempo</h5>
        </div>
        <div class="card-body">
            <canvas id="tendenciasChart" height="100"></canvas>
        </div>
    </div>

    {{-- Gráfica de barras --}}
    <div class="card" id="barChartCard" style="display:none;">
        <div class="card-header">
            <h5 class="card-title">Total por Artículo</h5>
        </div>
        <div class="card-body">
            <canvas id="barChart" height="80"></canvas>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });
    var lang = window.location.pathname.split('/')[1] || 'es';
    var lineChart = null;
    var barChartObj = null;

    $('#planta_id').on('change', function () {
        var idPlanta = $(this).val();
        var $maq = $('#maquina_id');

        if (!idPlanta) {
            $maq.empty().append('<option value="">Todas las máquinas</option>').prop('disabled', true);
            return;
        }

        $maq.empty().append('<option value="">Cargando...</option>').prop('disabled', true);
        $.get('/' + lang + '/corte/maquinas', { id_planta: idPlanta }, function (data) {
            $maq.empty().append('<option value="">Todas las máquinas</option>');
            data.forEach(function (m) {
                $maq.append('<option value="' + m.Id_Maquina + '">' + m.Txt_Nombre + '</option>');
            });
            $maq.prop('disabled', false);
        });
    });

    $('#btnConsultar').on('click', function () {
        var params = {
            id_planta: $('#planta_id').val(),
            id_maquina: $('#maquina_id').val(),
            meses: $('#meses').val()
        };

        $.get('/' + lang + '/reporte/tendencias/data', params, function (data) {
            $('#chartCard').show();
            $('#barChartCard').show();

            // Destruir charts anteriores
            if (lineChart) lineChart.destroy();
            if (barChartObj) barChartObj.destroy();

            // Gráfica de líneas
            var ctx = document.getElementById('tendenciasChart').getContext('2d');
            lineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Cantidad Consumida' } },
                        x: { title: { display: true, text: 'Periodo' } }
                    }
                }
            });

            // Gráfica de barras (totales por artículo)
            var barLabels = [];
            var barData = [];
            var barColors = [];
            data.datasets.forEach(function (ds) {
                barLabels.push(ds.label);
                var total = ds.data.reduce(function (a, b) { return a + b; }, 0);
                barData.push(total);
                barColors.push(ds.borderColor);
            });

            var ctx2 = document.getElementById('barChart').getContext('2d');
            barChartObj = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Total Consumido',
                        data: barData,
                        backgroundColor: barColors.map(function (c) { return c + '88'; }),
                        borderColor: barColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        });
    });
});

function goBack() { window.history.back(); }
</script>
@stop
