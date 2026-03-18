@extends('adminlte::page')

@section('title', 'Historial de Cortes')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Historial de Cortes de Resurtimiento
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
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
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
                <div class="col-md-2">
                    <label for="tipo_corte">Tipo:</label>
                    <select id="tipo_corte" class="form-control">
                        <option value="">Todos</option>
                        <option value="PRE">PRE</option>
                        <option value="POST">POST</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="startDate">Fecha Inicio:</label>
                    <input type="text" id="startDate" class="form-control" placeholder="AAAA-MM-DD">
                </div>
                <div class="col-md-3">
                    <label for="endDate">Fecha Fin:</label>
                    <input type="text" id="endDate" class="form-control" placeholder="AAAA-MM-DD">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button id="btnFiltrar" class="btn btn-primary btn-block"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-body">
            <table id="cortesTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th># Corte</th>
                        <th>Tipo</th>
                        <th>Máquina</th>
                        <th>Planta</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });

    $("#startDate, #endDate").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
    });

    var lang = window.location.pathname.split('/')[1] || 'es';

    var table = $('#cortesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/' + lang + '/corte/historial/data',
            data: function (d) {
                d.planta_id = $('#planta_id').val();
                d.tipo_corte = $('#tipo_corte').val();
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
            }
        },
        columns: [
            { data: 'Id_Corte' },
            {
                data: 'Tipo_Corte',
                render: function(data) {
                    var badge = data === 'PRE' ? 'badge-info' : 'badge-success';
                    return '<span class="badge ' + badge + '">' + data + '</span>';
                }
            },
            { data: 'Maquina' },
            { data: 'Planta' },
            {
                data: 'Fecha_Corte',
                render: function(data) {
                    if (!data) return '';
                    var d = new Date(data);
                    return d.toLocaleDateString('es-MX') + ' ' + d.toLocaleTimeString('es-MX', {hour: '2-digit', minute:'2-digit'});
                }
            },
            { data: 'Tipo_Usuario' },
            {
                data: null,
                render: function(data) {
                    var tipo = data.Tipo_Corte === 'PRE' ? 'pre' : 'post';
                    return '<a href="/' + lang + '/corte/' + tipo + '/' + data.Id_Corte + '/ver" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Ver</a>';
                },
                orderable: false,
                searchable: false
            }
        ],
        lengthMenu: [[50, 100, 500], [50, 100, 500]],
        pageLength: 50,
        responsive: true,
        language: { url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" }
    });

    $('#btnFiltrar').on('click', function () { table.draw(); });
    $('#planta_id, #tipo_corte').on('change', function () { table.draw(); });
});

function goBack() { window.history.back(); }
</script>
@stop
