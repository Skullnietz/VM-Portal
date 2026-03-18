@extends('adminlte::page')

@section('title', 'Discrepancias de Inventario')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Discrepancias de Inventario
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
                <div class="col-md-4">
                    <label for="planta_id">Planta:</label>
                    <select id="planta_id" class="form-control select2">
                        <option value="">Seleccione una planta</option>
                        @foreach($plantas as $planta)
                            <option value="{{ $planta->Id_Planta }}">{{ $planta->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="maquina_id">Máquina:</label>
                    <select id="maquina_id" class="form-control select2" disabled>
                        <option value="">Primero seleccione una planta</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="btnAnalizar" class="btn btn-primary" disabled>
                        <i class="fas fa-search"></i> Analizar Discrepancias
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="alert alert-light border mb-3" id="leyenda" style="display:none;">
        <strong>Leyenda de Discrepancia:</strong>
        <span class="badge badge-success ml-2">0</span> Sin discrepancia
        <span class="badge badge-warning ml-2">1-2</span> Menor
        <span class="badge badge-danger ml-2">3+</span> Significativa
        <br><small class="text-muted">Discrepancia = Stock Teórico - Stock Actual. Un valor positivo indica pérdida/merma.</small>
    </div>

    {{-- Tabla --}}
    <div class="card" id="resultCard" style="display:none;">
        <div class="card-header">
            <h5 class="card-title">Resultados de Discrepancia</h5>
            <div class="card-tools">
                <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Imprimir</button>
            </div>
        </div>
        <div class="card-body p-0">
            <table id="discrepanciaTable" class="table table-striped table-bordered mb-0" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>Selección</th>
                        <th>Charola</th>
                        <th>Artículo</th>
                        <th>Talla</th>
                        <th class="text-center">Último Relleno</th>
                        <th class="text-center">Consumos</th>
                        <th class="text-center">Stock Teórico</th>
                        <th class="text-center">Stock Actual</th>
                        <th class="text-center">Discrepancia</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-info" id="noDataMsg" style="display:none;">
        Seleccione una planta y máquina para analizar las discrepancias de inventario.
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    @media print {
        .no-print, .btn, .card-header .card-tools, form, #leyenda { display: none !important; }
    }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });
    var lang = window.location.pathname.split('/')[1] || 'es';

    // Cargar máquinas al seleccionar planta
    $('#planta_id').on('change', function () {
        var idPlanta = $(this).val();
        var $maq = $('#maquina_id');
        $maq.empty().append('<option value="">Cargando...</option>').prop('disabled', true);
        $('#btnAnalizar').prop('disabled', true);

        if (!idPlanta) return;

        $.get('/' + lang + '/corte/maquinas', { id_planta: idPlanta }, function (data) {
            $maq.empty().append('<option value="">Seleccione una máquina</option>');
            data.forEach(function (m) {
                $maq.append('<option value="' + m.Id_Maquina + '">' + m.Txt_Nombre + '</option>');
            });
            $maq.prop('disabled', false);
        });
    });

    $('#maquina_id').on('change', function () {
        $('#btnAnalizar').prop('disabled', !$(this).val());
    });

    $('#btnAnalizar').on('click', function () {
        var idMaquina = $('#maquina_id').val();
        if (!idMaquina) return;

        $('#resultCard').show();
        $('#leyenda').show();
        $('#noDataMsg').hide();

        $.get('/' + lang + '/reporte/discrepancias/data', { id_maquina: idMaquina }, function (response) {
            var tbody = $('#discrepanciaTable tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.append('<tr><td colspan="9" class="text-center">No se encontraron datos</td></tr>');
                return;
            }

            response.data.forEach(function (row) {
                var disc = parseInt(row.Discrepancia) || 0;
                var rowClass = '';
                var badgeClass = 'badge-success';

                if (Math.abs(disc) >= 3) {
                    rowClass = 'table-danger';
                    badgeClass = 'badge-danger';
                } else if (Math.abs(disc) >= 1) {
                    rowClass = 'table-warning';
                    badgeClass = 'badge-warning';
                }

                tbody.append(
                    '<tr class="' + rowClass + '">' +
                    '<td class="font-weight-bold">' + row.Seleccion + '</td>' +
                    '<td>' + row.Charola + '</td>' +
                    '<td>' + row.Articulo + '</td>' +
                    '<td>' + (row.Talla || 'N/A') + '</td>' +
                    '<td class="text-center">' + row.Stock_Ultimo_Relleno + '</td>' +
                    '<td class="text-center">' + row.Consumos_Registrados + '</td>' +
                    '<td class="text-center font-weight-bold">' + row.Stock_Teorico + '</td>' +
                    '<td class="text-center">' + row.Stock_Actual + '</td>' +
                    '<td class="text-center"><span class="badge ' + badgeClass + ' p-2">' + disc + '</span></td>' +
                    '</tr>'
                );
            });
        });
    });
});

function goBack() { window.history.back(); }
</script>
@stop
