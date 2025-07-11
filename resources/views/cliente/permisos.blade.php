@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Permisos de articulos'))

@section('content_header')
<div class="container">
        <div class="row">
            <!-- Columna de la izquierda con contenido alineado a la izquierda -->
            <div class="col-9 d-flex align-items-center">
                <h4 class="mb-0">
                    <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Permisos de Articulos') }}
                </h4>
            </div>

            <!-- Columna de la derecha con contenido alineado a la derecha -->
            <div class="col-3 d-flex justify-content-end align-items-center">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPermissionModal">
                        Agregar Permiso &nbsp;&nbsp;&nbsp;<i class="fas fa-user-lock"></i>
                    </button>
                    <a href="{{ url('export-excel-permissions') }}" type="button" class="btn btn-success">
                        Reporte <i class="fas fa-file-excel"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

<!-- Modal para Agregar Permiso -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" role="dialog" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addPermissionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">Agregar Permiso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="area">Área</label>
                        <select name="Id_Area" class="form-control select3" required>
                            @foreach($areas as $area)
                                <option value="{{ $area->Id_Area }}">{{ $area->Txt_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="articulo">Artículo</label>
                        <select name="Id_Articulo" class="form-control select3" required>
                            @foreach($articulos as $articulo)
                                <option value="{{ $articulo->Id_Articulo }}">{{ $articulo->Txt_Descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frecuencia">Frecuencia (días)</label>
                        <input type="number" name="Frecuencia" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad (pzas)</label>
                        <input type="number" name="Cantidad" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="submitBtn" class="btn btn-primary">Guardar</button>
                </div>
            </form>
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
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
        #areasTable {
    width: 100% !important; /* Asegura que la tabla ocupe todo el ancho disponible */
}

.dataTables_scroll {
    overflow-x: auto; /* Permite el desplazamiento horizontal si es necesario */
}

.dataTables_scrollHead,
.dataTables_scrollBody {
    overflow-x: auto; /* Asegura que el scroll horizontal esté disponible */
}
    </style>
@stop

@section('js')
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Script para inicializar Select2 -->
<!-- Script para inicializar Select2 y manejar la confirmación -->
<!-- Script para inicializar Select2 y manejar la confirmación en tiempo real -->
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select3').select2({
            width: '100%' // Ajusta el ancho según sea necesario
        });

        $('#submitBtn').click(function(e) {
            e.preventDefault();

            let form = $('#addPermissionForm');
            let actionUrl = "{{ url('add-permission') }}";

            // Verificar si ya existe el permiso antes de enviar
            $.ajax({
                url: "{{ url('check-permission') }}", // Ruta que verifica duplicados
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.exists) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Este artículo ya está registrado para esta área.',
                        });
                    } else {
                        // Si no existe el permiso, enviamos el formulario por AJAX
                        $.ajax({
                            url: actionUrl,
                            method: 'POST',
                            data: form.serialize(),
                            success: function(result) {
                                if(result.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Permiso agregado exitosamente.',
                                    }).then(() => {
                                        // Actualizar la tabla de permisos sin recargar la página
                                        $('#permisos-articulos-table').DataTable().ajax.reload();
                                        $('#addPermissionModal').modal('hide'); // Cerrar el modal
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Hubo un problema al agregar el permiso.',
                                    });
                                }
                            }
                        });
                    }
                }
            });
        });
    });
</script>
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
            { data: 'Nombre' },
            { data: 'Articulo' },
            { 
                data: 'Frecuencia',
                render: function(data, type, row) {
                    return `
                        <div class="input-group">
                            <input type="number"  min="0" class="form-control update-frecuencia" data-id="${row.Clave}" value="${data}">
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
                            <input type="number" max="99" min="0" class="form-control update-cantidad" data-id="${row.Clave}" value="${data}">
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
                    return `<button class="btn btn-danger btn-xs delete-btn" data-id="${row.Clave}">&nbsp;&nbsp;&nbsp; Eliminar &nbsp;&nbsp;&nbsp;<i class="fas fa-trash"></i></button>`;
                }
            }
        ],
            responsive: true,
            scrollX: true,
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
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Una vez eliminado, no podrás recuperar este permiso.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'Cancelar',
        dangerMode: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/delete-permiso-articulo/${id}`,
                type: 'POST',
                success: function(result) {
                    $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
                    Swal.fire(
                        'Eliminado',
                        'Permiso eliminado con éxito',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error',
                        `Error eliminando el permiso: ${xhr.responseJSON.error || xhr.responseText}`,
                        'error'
                    );
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
            $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
            Swal.fire(
                'Actualizado',
                'Estado actualizado con éxito',
                'success'
            );
        },
        error: function(xhr, status, error) {
            Swal.fire(
                'Error',
                `Error actualizando el estado: ${xhr.responseJSON.error || xhr.responseText}`,
                'error'
            );
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
            // Mostrar Toast de éxito
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Actualizado',
                body: 'Cantidad actualizada con éxito',
                autohide: true,
                delay: 3000
            });
        },
        error: function(xhr, status, error) {
            // Mostrar Toast de error
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error',
                body: `Error actualizando la cantidad: ${xhr.responseJSON?.error || xhr.responseText}`,
                autohide: true,
                delay: 5000
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
            $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
            
            // Mostrar Toast de éxito
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Actualizado',
                body: 'Frecuencia actualizada con éxito',
                autohide: true,
                delay: 3000
            });
        },
        error: function(xhr, status, error) {
            // Mostrar Toast de error
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error',
                body: `Error actualizando la frecuencia: ${xhr.responseJSON?.error || xhr.responseText}`,
                autohide: true,
                delay: 5000
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
