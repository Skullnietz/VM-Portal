@extends('adminlte::page')

@section('title', __('Reporte Historial de Relleno'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                        class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Reporte de Historial de Relleno') }}
            </h4>
        </div>
        <div class="col text-right">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimir
            </button>
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
                <form method="GET" action="{{ route('historialrelleno.index') }}" id="plant-form">
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
                        <div class="col-md-3">
                            <label for="startDate">Fecha Inicio:</label>
                            <input type="text" id="startDate" class="form-control" placeholder="AAAA-MM-DD">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">Fecha Fin:</label>
                            <input type="text" id="endDate" class="form-control" placeholder="AAAA-MM-DD">
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
                        <h5 class="card-title">Historial de Relleno</h5>
                    </div>
                    <div class="card-body">
                        <table id="historyTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Máquina</th>
                                    <th>Artículo</th>
                                    <th>Cantidad Anterior</th>
                                    <th>Cantidad Rellenada</th>
                                    <th>Cantidad Nueva</th>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Tipo Usuario</th>
                                </tr>
                            </thead>
                        </table>
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
    @media print {

        .no-print,
        .card-header,
        .btn,
        form {
            display: none !important;
        }

        .card,
        .card-body {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@stop

@section('js')
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
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
            var table = $('#historyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("historialrelleno.data") }}',
                    data: function (d) {
                        d.planta_id = '{{ request("planta_id") }}';
                        d.startDate = $('#startDate').val();
                        d.endDate = $('#endDate').val();
                    }
                },
                lengthMenu: [[100, 500, 1000, -1], [100, 500, 1000, "Todos"]],
                pageLength: 100,
                columns: [
                    { data: 'Maquina' },
                    { data: 'Articulo' },
                    { data: 'Cantidad_Anterior' },
                    { data: 'Cantidad_Rellenada' },
                    { data: 'Cantidad_Nueva' },
                    { data: 'Fecha_Relleno' },
                    { data: 'Usuario' },
                    { data: 'Tipo_Usuario' }
                ],
                responsive: true,
                scrollX: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                }
            });

        @endif
        });

    function goBack() {
        window.history.back();
    }
</script>
@stop