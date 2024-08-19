@extends('adminlte::page')

@section('title', __('Areas'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Areas') }}
            </h4>
        </div>
        <div class="col text-right">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" id="enableEditBtn" class="btn btn-secondary">
                    Habilitar Edición &nbsp;&nbsp;&nbsp;<i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAreaModal">
                    Agregar Area &nbsp;&nbsp;&nbsp;<i class="fas fa-user-plus"></i>
                </button>
                <a href="{{ url('export-excel-areas') }}" type="button" class="btn btn-success">
                    Reporte de Áreas &nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal para agregar área -->
<div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog" aria-labelledby="addAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAreaModalLabel">Agregar Área</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addAreaForm">
                    <div class="form-group">
                        <label for="areaName">Nombre del Área</label>
                        <input type="text" class="form-control" id="areaName" name="areaName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
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
                        <table id="areasTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID Área</th>
                                    <th>Nombre</th>
                                    <th>Estatus</th> 
                                    <th>Fecha de Alta</th>
                                    <th>Fecha de Modificación</th>
                                    <th>Acciones</th>
                                    <th>Permisos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Contenido manejado por DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Área</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editIdArea" name="id_area">
                    <div class="form-group">
                        <label for="editNombre">Nombre</label>
                        <input type="text" class="form-control" id="editNombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="editEstatus">Estatus</label>
                        <select class="form-control" id="editEstatus" name="estatus" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveChangesBtn">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
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
    <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
    var table = $('#areasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url("get-areas/data") }}',
        columns: [
            { data: 'Id_Area' },
            {
                data: 'Txt_Nombre',
                render: function(data, type, row) {
                    return '<input type="text" class="form-control editable-name" data-id="' + row.Id_Area + '" value="' + data + '" disabled>';
                }
            },
            {
                data: 'Txt_Estatus',
                render: function(data, type, row) {
                    var buttonClass = data === 'Alta' ? 'btn-success' : 'btn-danger';
                    var newStatus = data === 'Alta' ? 'Baja' : 'Alta';
                    return '<button class="btn ' + buttonClass + ' toggle-status" data-id="' + row.Id_Area + '" data-new-status="' + newStatus + '">' + data + '</button>';
                },
                orderable: false,
                searchable: false
            },
            { data: 'AFecha' },
            { data: 'MFecha' },
            {
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-danger delete-area" data-id="' + row.Id_Area + '"><i class="fas fa-trash"></i></button>';
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data, type, row) {
                    return '<a href="{{ url("areas/permissions") }}/' + row.Id_Area + '" class="btn btn-info"><i class="fas fa-lock"></i> Permisos</a>';
                },
                orderable: false,
                searchable: false
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

    // Manejar el envío del formulario para agregar un área
    $('#addAreaForm').on('submit', function(e) {
        e.preventDefault(); // Evitar el comportamiento por defecto del formulario

        var areaName = $('#areaName').val();

        $.ajax({
            url: '{{ url("areas/add") }}', // Ruta para agregar el área
            method: 'POST',
            data: {
                new_name: areaName,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Área agregada exitosamente.',
                        icon: 'success'
                    }).then(() => {
                        $('#addAreaModal').modal('hide');
                        table.ajax.reload(); // Recargar la tabla
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al agregar el área.',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error en la solicitud AJAX.',
                    icon: 'error'
                });
            }
        });
    });

    // Manejar la eliminación de un área
    $('#areasTable').on('click', '.delete-area', function() {
        var idArea = $(this).data('id');

        Swal.fire({
            title: 'Confirmar eliminación',
            text: "¿Estás seguro de que deseas eliminar esta área?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("areas/delete") }}',
                    method: 'POST',
                    data: {
                        id_area: idArea,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Eliminado',
                                    'El área ha sido eliminada correctamente.',
                                    'success'
                                );
                                table.ajax.reload(); // Recargar la tabla
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                            }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error en la solicitud AJAX.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    

    // Manejar el cambio entre Habilitar y Deshabilitar
    $('#enableEditBtn').on('click', function() {
        var isDisabled = $('.editable-name').first().prop('disabled');

        // Alternar el estado de los campos
        $('.editable-name').prop('disabled', !isDisabled);

        // Cambiar el texto y el ícono del botón
        if (isDisabled) {
            $(this).text('Deshabilitar Edición ');
            $(this).append('<i class="fas fa-edit"></i>');
            $(this).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            $(this).text('Habilitar Edición ');
            $(this).append('<i class="fas fa-edit"></i>');
            $(this).removeClass('btn-danger').addClass('btn-secondary');
        }
    });

    // Manejar el cambio de estatus
    $('#areasTable').on('click', '.toggle-status', function() {
        var idArea = $(this).data('id');
        var newStatus = $(this).data('new-status');
        var button = $(this);

        Swal.fire({
            title: 'Confirmar cambio de estatus',
            text: "¿Estás seguro de que deseas cambiar el estatus a " + newStatus + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("areas/update-status") }}',
                    method: 'POST',
                    data: {
                        id_area: idArea,
                        new_status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text(newStatus);
                            button.toggleClass('btn-success btn-danger');
                            button.data('new-status', response.new_status === 'Alta' ? 'Baja' : 'Alta');
                            Swal.fire(
                                'Cambio realizado',
                                'El estatus se ha cambiado correctamente.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error',
                                'Error al cambiar el estatus.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error en la solicitud AJAX.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Manejar la edición de nombre
    $('#areasTable').on('change', '.editable-name', function() {
        var idArea = $(this).data('id');
        var newName = $(this).val();

        Swal.fire({
            title: 'Confirmar edición',
            text: "¿Estás seguro de que deseas cambiar el nombre a " + newName + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("areas/update-name") }}',
                    method: 'POST',
                    data: {
                        id_area: idArea,
                        new_name: newName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (!response.success) {
                            Swal.fire(
                                'Error',
                                'Error al actualizar el nombre.',
                                'error'
                            );
                        } else {
                            Swal.fire(
                                'Actualizado',
                                'El nombre se ha actualizado correctamente.',
                                'success'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error en la solicitud AJAX.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});

function goBack() {
    window.history.back();
}

</script>
@stop
