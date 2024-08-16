@extends('adminlte::page')

@section('title', __('Areas'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Areas') }}</h4>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Areas</h5>
                    </div>
                    <div class="card-body">
                        <table id="areasTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID Área</th>
                                    <th>Nombre</th>
                                    <th>Estatus</th>
                                    <th>Fecha Alta</th>
                                    <th>Fecha Modificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán aquí con DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
@stop

@section('js')
    <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#areasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url("get-areas/data") }}', // Reemplaza esto con la ruta correcta
                columns: [
                    { data: 'Id_Area' },
                    { data: 'Txt_Nombre' },
                    { data: 'Txt_Estatus' },
                    { data: 'AFecha' },
                    { data: 'MFecha' }
                ]
            });
        });

        function goBack() {
            window.history.back();
        }
    </script>
@stop
