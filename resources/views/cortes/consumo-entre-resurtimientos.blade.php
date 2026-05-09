@extends('adminlte::page')

@section('title', 'Consumo entre Resurtimientos')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Consumo entre Resurtimientos
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
                    <button id="btnConsultar" class="btn btn-primary" disabled>
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card" id="resultCard" style="display:none;">
        <div class="card-header">
            <h5 class="card-title">Consumo por Periodo de Resurtimiento</h5>
            <div class="card-tools">
                <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Imprimir</button>
            </div>
        </div>
        <div class="card-body p-0">
            <table id="consumoTable" class="table table-striped table-bordered mb-0" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha Resurtimiento</th>
                        <th>Artículo</th>
                        <th class="text-center">Total Consumido</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });
    var lang = window.location.pathname.split('/')[1] || 'es';

    $('#planta_id').on('change', function () {
        var idPlanta = $(this).val();
        var $maq = $('#maquina_id');
        $maq.empty().append('<option value="">Cargando...</option>').prop('disabled', true);
        $('#btnConsultar').prop('disabled', true);

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
        $('#btnConsultar').prop('disabled', !$(this).val());
    });

    $('#btnConsultar').on('click', function () {
        var idMaquina = $('#maquina_id').val();
        if (!idMaquina) return;

        $('#resultCard').show();

        $.get('/' + lang + '/reporte/consumo-entre-resurtimientos/data', { id_maquina: idMaquina }, function (response) {
            var tbody = $('#consumoTable tbody');
            tbody.empty();

            if (response.data.length === 0) {
                tbody.append('<tr><td colspan="3" class="text-center">No se encontraron datos de consumo entre resurtimientos</td></tr>');
                return;
            }

            response.data.forEach(function (row) {
                tbody.append(
                    '<tr>' +
                    '<td>' + row.Fecha_Resurtimiento + '</td>' +
                    '<td>' + row.Articulo + '</td>' +
                    '<td class="text-center font-weight-bold">' + row.Total_Consumido + '</td>' +
                    '</tr>'
                );
            });
        });
    });
});

function goBack() { window.history.back(); }
</script>
@stop
