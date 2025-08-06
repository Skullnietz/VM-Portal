@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Operadores'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Operadores') }}
            </h4>
        </div>
        <div class="col text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                Agregar Operador&nbsp;&nbsp;&nbsp;<i class="fas fa-user-plus"></i>
            </button>
        </div>
    </div>
</div>
@stop

@section('content')
<!-- Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Agregar Operador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidoP">Apellido Paterno</label>
                        <input type="text" class="form-control" id="apellidoP" name="apellidoP" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidoM">Apellido Materno</label>
                        <input type="text" class="form-control" id="apellidoM" name="apellidoM" >
                    </div>
                    <div class="form-group">
                        <label for="Nick_Usuario">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="Nick_Usuario" name="Nick_Usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="puesto">Puesto</label>
                        <input type="text" class="form-control" id="puesto" name="puesto" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="form-group">
                        <label for="plantas">Plantas con Acceso</label>
                        <select id="plantas" name="plantas[]" multiple class="form-control">
                            </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveOp">Guardar Operador</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Editar -->
<div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Editar Operador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAdminForm">
                    @csrf
                    <input type="hidden" id="edit_id_operador" name="id_operador">
                    <div class="form-group">
                        <label for="edit_nombre">Nombre</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_apellidoP">Apellido Paterno</label>
                        <input type="text" class="form-control" id="edit_apellidoP" name="apellidoP" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_apellidoM">Apellido Materno</label>
                        <input type="text" class="form-control" id="edit_apellidoM" name="apellidoM">
                    </div>
                    <div class="form-group">
                        <label for="edit_puesto">Puesto</label>
                        <input type="text" class="form-control" id="edit_puesto" name="puesto" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_plantas">Plantas con Acceso</label>
                        <select id="edit_plantas" name="plantas[]" multiple></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="updateOp">Actualizar Operador</button>
            </div>
        </div>
    </div>
</div>

    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tabla de Operadores</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="OpTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Nombre de Usuario</th>
                                    <th>Puesto</th>
                                    <th>Plantas</th>
                                    <th>Estatus</th>
                                    <th>Opciones</th>
                                    <th>Usuario Alta</th>
                                    <th>Fecha Alta</th>
                                    <th>Usuario Modificación</th>
                                    <th>Fecha Modificación</th>
                                    <th>Usuario Baja</th>
                                    <th>Fecha Baja</th>
                                    
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@stop

@section('css')
    <!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />



    <style>
        .alta {
            background-color: #d4edda; /* Verde */
            color: #155724;
        }

        .modificacion {
            background-color: #fff3cd; /* Amarillo */
            color: #856404;
        }

        .baja {
            background-color: #f8d7da; /* Rojo */
            color: #721c24;
        }
    </style>
@stop

@section('js')
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    $(document).ready(function() {
        let choicesInstanceAdd = null;
        let choicesInstanceEdit = null;

        // Inicializar DataTable
        $('#OpTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('get-operadores') }}',
                type: 'POST',
                data: function (d) {
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                { data: 'NombreCompleto', name: 'NombreCompleto' },
                { data: 'NombreUsuario', name: 'NombreUsuario' },
                { data: 'Txt_Puesto', name: 'puesto' },
                { 
                    data: 'PlantasConAcceso', 
                    name: 'PlantasConAcceso',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data;
                    }
                },
                {
                    data: 'estatus_icon',
                    name: 'estatus_icon',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let iconClass = row.Txt_Estatus === 'Alta' ? 'fa-toggle-on fa-2x text-success' : 'fa-toggle-off fa-2x text-danger';
                        let nuevoEstatus = row.Txt_Estatus === 'Alta' ? 'Baja' : 'Alta';
                        return `<i id="estatus-icon-${row.id}" class="fas ${iconClass}" style="cursor: pointer;" onclick="toggleEstatus(${row.id}, '${nuevoEstatus}')"></i>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <button class="btn btn-danger btn-sm" onclick="deleteAdmin(${row.id})" title="Eliminar">
                                    <i class="fas fa-trash fa-2x"></i>
                                </button>
                                <button class="btn btn-warning btn-sm btnEditarOperador" title="Editar"
                                    data-operador='${JSON.stringify({
                                        id: row.id,
                                        Txt_Nombre: row.Txt_Nombre,
                                        Txt_ApellidoP: row.Txt_ApellidoP,
                                        Txt_ApellidoM: row.Txt_ApellidoM,
                                        Nick_Usuario: row.Nick_Usuario,
                                        Txt_Puesto: row.Txt_Puesto,
                                        IdsPlantas: row.IdsPlantas
                                    })}'>
                                    <i class="fas fa-edit fa-2x"></i>
                                </button>
                            </div>
                        `;
                    }
                },
                { data: 'UsuarioAlta', name: 'UsuarioAlta' },
                { data: 'Fecha_Alta', name: 'Fecha_Alta' },
                { data: 'UsuarioModificacion', name: 'UsuarioModificacion' },
                { data: 'Fecha_Modificacion', name: 'Fecha_Modificacion' },
                { data: 'UsuarioBaja', name: 'UsuarioBaja' },
                { data: 'Fecha_Baja', name: 'Fecha_Baja' }
            ],
            createdRow: function(row, data, dataIndex) {
                $('td', row).eq(6).addClass('alta');
                $('td', row).eq(7).addClass('alta');
                $('td', row).eq(8).addClass('modificacion');
                $('td', row).eq(9).addClass('modificacion');
                $('td', row).eq(10).addClass('baja');
                $('td', row).eq(11).addClass('baja');
            },
            order: [[0, 'asc']],
            responsive: true,
            scrollX: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
        });

        // Guardar operador
        $('#saveOp').on('click', function() {
            let password = $('#password').val();
            let confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden.');
                return;
            }

            let formData = $('#addAdminForm').serialize();

            $.ajax({
                url: '/operadores/add',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#addAdminModal').modal('hide');
                    $('#OpTable').DataTable().ajax.reload();
                    toastr.success(response.message); // Aquí se muestra el mensaje de éxito
                    $('#addAdminForm')[0].reset();
                },
                error: function(xhr) {
                    let errorMessage = 'Error al agregar operador.\n';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMessage += value.join(', ') + '\n';
                        });
                    } else if (xhr.responseJSON?.message) {
                        errorMessage += xhr.responseJSON.message;
                    } else {
                        errorMessage += 'Error desconocido.';
                    }
                    alert(errorMessage);
                }
            });
        });

        // Editar operador - abrir modal
        $(document).on('click', '.btnEditarOperador', function () {
            let operador = $(this).data('operador');

            $('#edit_id_operador').val(operador.id);
            $('#edit_nombre').val(operador.Txt_Nombre);
            $('#edit_apellidoP').val(operador.Txt_ApellidoP);
            $('#edit_apellidoM').val(operador.Txt_ApellidoM || '');
            $('#edit_usuario').val(operador.Nick_Usuario);
            $('#edit_puesto').val(operador.Txt_Puesto);

            // Obtener las plantas y cargar opciones
            $.ajax({
                $.ajax({
                // Obtener las plantas y cargar opciones
            $.ajax({
                url: '{{ route('get-plantas') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    let selectEdit = $('#edit_plantas');
                    selectEdit.empty();

                    $.each(data, function(index, planta) {
                        selectEdit.append(`<option value="${planta.Id_Planta}">${planta.Txt_Nombre_Planta} (${planta.Id_Planta})</option>`);
                    });

                    if (choicesInstanceEdit) choicesInstanceEdit.destroy();
                    choicesInstanceEdit = new Choices(selectEdit[0], {
                        removeItemButton: true,
                        placeholder: true,
                        placeholderValue: 'Selecciona las plantas',
                        noResultsText: 'No se encontraron plantas'
                    });

                    let plantasAcceso = operador.IdsPlantas ? operador.IdsPlantas.split(',') : [];

                    if (choicesInstanceEdit) choicesInstanceEdit.destroy();

                    choicesInstanceEdit = new Choices(selectEdit[0], {
                        removeItemButton: true,
                        placeholder: true,
                        placeholderValue: 'Selecciona las plantas',
                        noResultsText: 'No se encontraron plantas'
                    });

                    // Timeout para esperar a que Choices lo re-renderice
                    setTimeout(() => {
                        plantasAcceso.forEach(id => {
                            choicesInstanceEdit.setChoiceByValue(id);
                        });
                    }, 100);
                }
            });

            $('#editAdminModal').modal('show');
        });

        // Actualizar operador
        $('#updateOp').click(function () {
            let formData = $('#editAdminForm').serialize();
            $.ajax({
                url: '/admin/editar-operador',
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $('#editAdminModal').modal('hide');
                        toastr.success(response.message);
                        $('#OpTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error('Error al actualizar operador.');
                }
            });
        });

        // Modal: cargar plantas para agregar operador
        $('#addAdminModal').on('show.bs.modal', function () {
            let plantasSelect = $('#plantas');
            plantasSelect.empty();

            $.ajax({
        
                url: '{{ route('get-plantas') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    $.each(data, function(key, planta) {
                        plantasSelect.append(`<option value="${planta.Id_Planta}">${planta.Txt_Nombre_Planta} (${planta.Id_Planta})</option>`);
                    });
                    $('#OpTable').DataTable().ajax.reload();
                    if (choicesInstanceAdd) choicesInstanceAdd.destroy();
                    choicesInstanceAdd = new Choices(plantasSelect[0], {
                        removeItemButton: true,
                        searchPlaceholderValue: 'Buscar plantas...',
                        noResultsText: 'No se encontraron plantas',
                        placeholder: true,
                        placeholderText: 'Selecciona las plantas'
                    });
                },
                error: function(xhr) {
                    console.error('Error al cargar las plantas:', xhr);
                }
            });
        });

        $('#addAdminModal, #editAdminModal').on('hide.bs.modal', function () {
            if (choicesInstanceAdd) {
                choicesInstanceAdd.destroy();
                choicesInstanceAdd = null;
            }
            if (choicesInstanceEdit) {
                choicesInstanceEdit.destroy();
                choicesInstanceEdit = null;
            }
        });
    });
    });
    });

    // Eliminar operador
    function deleteAdmin(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este operador?')) {
            $.ajax({
                url: `/operadores/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Operador eliminado exitosamente');
                    $('#OpTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('Error al eliminar operador: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    // Cambiar estatus
    function toggleEstatus(id, nuevoEstatus) {
        $.ajax({
            url: '{{ url("/operadores/estatus") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                nuevoEstatus: nuevoEstatus
            },
            success: function(response) {
                if (response.success) {
                    $('#OpTable').DataTable().ajax.reload();
                } else {
                    console.error('Error al actualizar el estatus:', response.message);
                }
            },
            error: function(xhr) {
                console.error('Error al actualizar el estatus:', xhr.responseText);
            }
        });
    }
</script>


@stop
