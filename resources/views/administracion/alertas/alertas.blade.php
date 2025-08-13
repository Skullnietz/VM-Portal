@extends('adminlte::page')

@section('title', 'Alertas')

@section('content_header')
    <h1>Gestión de Alertas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Alertas</h3>
        </div>
        <div class="card-body">
            <table id="alertasTable" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Planta</th>
                        <th>Frecuencia</th>
                        <th>Email</th>
                        <th>Notificaciones</th>
                        <th>Creación</th>
                        <th>Actualización</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    {{-- Agrega aquí estilos personalizados si es necesario --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#alertasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/allalertas",
                type: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';  // Asegurando que CSRF se pase correctamente
                    return d;
                }
            },
            columns: [
                { data: 'Id', name: 'Id' },
                { data: 'NombreCompleto', name: 'NombreCompleto', render: function(data, type, row) {
                    return data + ' (' + row.Nick_Usuario + ')';
                }},
                { data: 'Txt_Nombre_Planta', name: 'Txt_Nombre_Planta' },
                { data: 'Frecuencia', name: 'Frecuencia' },
                { data: 'Email', name: 'Email' },
                { data: 'Recibir_Notificaciones', name: 'Recibir_Notificaciones', render: function(data, type, row){
                    return data == '1' ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>';
                }},
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                    return '<a href="#" class="btn btn-primary btn-sm">Editar</a>';
                }}
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            }
        });
    });
    </script>
@stop
