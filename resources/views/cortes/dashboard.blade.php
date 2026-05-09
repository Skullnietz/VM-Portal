@extends('adminlte::page')

@section('title', 'Dashboard Operativo')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Dashboard Operativo
            </h4>
        </div>
        <div class="col text-right">
            <select id="planta_filter" class="form-control d-inline-block" style="width: auto;">
                <option value="">Todas las plantas</option>
                @foreach($plantas as $planta)
                    <option value="{{ $planta->Id_Planta }}">{{ $planta->Nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- KPI Cards --}}
    <div class="row" id="kpiRow">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="kpi_bajo_stock">-</h3>
                    <p>Máquinas con Stock Bajo</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="kpi_consumo">-</h3>
                    <p>Consumido (30 días)</p>
                </div>
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="kpi_resurtido">-</h3>
                    <p>Resurtido (30 días)</p>
                </div>
                <div class="icon"><i class="fas fa-box-open"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="kpi_alertas">-</h3>
                    <p>Alertas Activas</p>
                </div>
                <div class="icon"><i class="fas fa-bell"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Top artículos --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top 5 Artículos Más Consumidos (30 días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="topArticulosChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Consumo diario --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Consumo Diario (Últimos 7 días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="consumoDiarioChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas de stock bajo --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-exclamation-circle"></i> Alertas de Stock Bajo</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered mb-0" id="alertasTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Máquina</th>
                                <th>Artículo</th>
                                <th>Selección</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Mínimo</th>
                                <th class="text-center">Máximo</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .small-box { border-radius: 8px; }
    .small-box .inner h3 { font-size: 2.2rem; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function () {
    var lang = window.location.pathname.split('/')[1] || 'es';
    var topChart = null;
    var dailyChart = null;

    function loadDashboard() {
        var idPlanta = $('#planta_filter').val();

        $.get('/' + lang + '/dashboard-operativo/data', { id_planta: idPlanta }, function (data) {
            // KPIs
            $('#kpi_bajo_stock').text(data.maquinas_bajo_stock);
            $('#kpi_consumo').text(data.total_consumo_30d);
            $('#kpi_resurtido').text(data.total_resurtido_30d);
            $('#kpi_alertas').text(data.alertas.length);

            // Top artículos chart
            if (topChart) topChart.destroy();
            var topLabels = data.top_articulos.map(function (a) { return a.Txt_Descripcion; });
            var topData = data.top_articulos.map(function (a) { return a.Total; });
            var topColors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8'];

            topChart = new Chart(document.getElementById('topArticulosChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: topLabels,
                    datasets: [{
                        data: topData,
                        backgroundColor: topColors,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // Consumo diario chart
            if (dailyChart) dailyChart.destroy();
            var dailyLabels = data.consumo_diario.map(function (d) {
                var date = new Date(d.Fecha);
                return date.toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric' });
            });
            var dailyData = data.consumo_diario.map(function (d) { return d.Total; });

            dailyChart = new Chart(document.getElementById('consumoDiarioChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Consumo',
                        data: dailyData,
                        backgroundColor: '#007bff88',
                        borderColor: '#007bff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Alertas table
            var tbody = $('#alertasTable tbody');
            tbody.empty();

            if (data.alertas.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center text-success"><i class="fas fa-check-circle"></i> Sin alertas de stock bajo</td></tr>');
            } else {
                data.alertas.forEach(function (a) {
                    var pct = a.Cantidad_Max > 0 ? Math.round((a.Stock / a.Cantidad_Max) * 100) : 0;
                    var statusBadge = a.Stock === 0
                        ? '<span class="badge badge-danger">VACÍO</span>'
                        : '<span class="badge badge-warning">BAJO (' + pct + '%)</span>';

                    tbody.append(
                        '<tr>' +
                        '<td>' + a.Maquina + '</td>' +
                        '<td>' + a.Articulo + '</td>' +
                        '<td>' + a.Seleccion + '</td>' +
                        '<td class="text-center font-weight-bold text-danger">' + a.Stock + '</td>' +
                        '<td class="text-center">' + a.Cantidad_Min + '</td>' +
                        '<td class="text-center">' + a.Cantidad_Max + '</td>' +
                        '<td class="text-center">' + statusBadge + '</td>' +
                        '</tr>'
                    );
                });
            }
        });
    }

    loadDashboard();
    $('#planta_filter').on('change', loadDashboard);
});

function goBack() { window.history.back(); }
</script>
@stop
