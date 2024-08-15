@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Permisos de articulo'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Permisos de articulo') }}</h4>
            </div>
            <div class="col-md-3 col-3 ml-auto">
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
                        <h5 class="card-title">
                            Permisos de Articulo
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="#" class="dropdown-item">Action</a>
                                    <a href="#" class="dropdown-item">Another action</a>
                                    <a href="#" class="dropdown-item">Something else here</a>
                                    <a class="dropdown-divider"></a>
                                    <a href="#" class="dropdown-item">Separated link</a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body">
                    <table class="table table-bordered" id="permisos-articulos-table">
            <thead>
                <tr>
                    <th>Clave</th>
                    <th>Nombre</th>
                    <th>Artículo</th>
                    <th>Frecuencia</th>
                    <th>Cantidad</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $('#permisos-articulos-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/get-permisos-articulos',
            type: 'GET',
            dataSrc: function(json) {
                // Asegúrate de que el formato de datos es el esperado
                console.log(json);
                return json.data;
            }
        },
        columns: [
            { data: 'Clave' },
            { data: 'Nombre' },
            { data: 'Articulo' },
            { 
                data: 'Frecuencia',
                render: function(data, type, row) {
                    return `
                        <div class="input-group">
                            <input type="number" class="form-control update-frecuencia" data-id="${row.Clave}" value="${data}">
                            <div class="input-group-append">
                                <span class="input-group-text">Días</span>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Cantidad',
                render: function(data, type, row) {
                    return `
                        <div class="input-group">
                            <input type="number" class="form-control update-cantidad" data-id="${row.Clave}" value="${data}">
                            <div class="input-group-append">
                                <span class="input-group-text">Cantidad</span>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Estatus',
                render: function(data, type, row) {
                    var btnClass = data === 'Alta' ? 'btn-danger' : 'btn-success';
                    var btnText = data === 'Alta' ? 'Desactivar <i class="fas fa-lock"></i>' : '&nbsp;&nbsp;Activar&nbsp;&nbsp; <i class="fas fa-lock-open"></i>';
                    return `
                        <button class="btn btn-xs ${btnClass} toggle-status" data-id="${row.Clave}" data-status="${data}">
                            ${btnText}
                        </button>
                    `;
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                render: function(data, type, row) {
                    return `<button class="btn btn-danger btn-xs delete-btn" data-id="${row.Clave}">Eliminar</button>`;
                }
            }
        ],
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    });

    // Función para eliminar un permiso
    $('#permisos-articulos-table').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        swal({
            title: "¿Estás seguro?",
            text: "Una vez eliminado, no podrás recuperar este permiso.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: `/delete-permiso-articulo/${id}`,
                    type: 'DELETE',
                    success: function(result) {
                        table.ajax.reload();
                        swal("Permiso eliminado con éxito", {
                            icon: "success",
                        });
                    },
                    error: function(xhr, status, error) {
                        swal("Error eliminando el permiso: " + xhr.responseText, {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    // Función para cambiar el estado
    $('#permisos-articulos-table').on('click', '.toggle-status', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var newStatus = status === 'Alta' ? 'Baja' : 'Alta';
        $.ajax({
            url: `/toggle-status-permiso-articulo/${id}`,
            type: 'POST',
            data: {
                status: newStatus
            },
            success: function(result) {
                table.ajax.reload();
                swal("Estado actualizado con éxito", {
                    icon: "success",
                });
            },
            error: function(xhr, status, error) {
                swal("Error actualizando el estado: " + xhr.responseText, {
                    icon: "error",
                });
            }
        });
    });

    // Función para actualizar la cantidad en tiempo real
    $('#permisos-articulos-table').on('change', '.update-cantidad', function() {
        var id = $(this).data('id');
        var value = $(this).val();
        $.ajax({
            url: `/update-permiso-articulo/${id}`,
            type: 'POST',
            data: {
                field: 'Cantidad',
                value: value
            },
            success: function(result) {
                table.ajax.reload();
                swal("Cantidad actualizada con éxito", {
                    icon: "success",
                });
            },
            error: function(xhr, status, error) {
                swal("Error actualizando la cantidad: " + xhr.responseText, {
                    icon: "error",
                });
            }
        });
    });

    // Función para actualizar la frecuencia en tiempo real
    $('#permisos-articulos-table').on('change', '.update-frecuencia', function() {
        var id = $(this).data('id');
        var value = $(this).val();
        $.ajax({
            url: `/update-permiso-articulo/${id}`,
            type: 'POST',
            data: {
                field: 'Frecuencia',
                value: value
            },
            success: function(result) {
                table.ajax.reload();
                swal("Frecuencia actualizada con éxito", {
                    icon: "success",
                });
            },
            error: function(xhr, status, error) {
                swal("Error actualizando la frecuencia: " + xhr.responseText, {
                    icon: "error",
                });
            }
        });
    });
});


</script>


<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop