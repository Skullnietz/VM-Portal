@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Usuarios (InPlant)'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Usuarios (InPlant)') }}
            </h4>
        </div>
        <div class="col text-right">
            
            <button id="addusuario" type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                Agregar Usuario (InPlant)&nbsp;&nbsp;&nbsp;<i class="fas fa-user-plus"></i>
            </button>
            <button id="addplanta" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#addAdminModal">
                Agregar Planta &nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-industry"></i>
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
            <div class="text-center mb-3">
                <div class="row"><h2 style="margin-right:15px"><i class="fas fa-fw fa-industry"></i></h2>
                    <button type="button" class="btn btn-sm btn-success" id="existingPlantBtn" style="margin-right:15px">Planta Existente</button>
                    <button type="button" class="btn btn-sm btn-success" id="createPlantBtn">Agregar Planta</button></div>
            
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                

                <div id="plantCreationSection" style="display:none;">
                    <form id="createPlantForm">
                        @csrf <!-- Token CSRF -->
                        <div class="form-group">
                            <label for="txtNombrePlanta">Nombre de Planta</label>
                            <input type="text" class="form-control" id="txtNombrePlanta" name="txtNombrePlanta" required>
                        </div>
                        <div class="form-group">
                            <label for="txtCodigoCliente">Código Cliente</label>
                            <input type="text" class="form-control" id="txtCodigoCliente" name="txtCodigoCliente" required>
                        </div>
                        <div class="form-group">
                            <label for="txtSitio">Sitio</label>
                            <input type="text" class="form-control" id="txtSitio" name="txtSitio" required>
                        </div>
                    </form>
                </div>

                <div id="adminCreationSection" style="display:none;">
                    <form id="addAdminForm">
                        @csrf <!-- Token CSRF -->
                        <div class="form-group">
                        <label for="planta">Planta</label>
                        <select class="form-control select2" id="planta" name="planta" style="width: 100%;" required>
                            <option value="" disabled selected>Seleccione una planta</option>
                            <!-- Las opciones se llenarán dinámicamente -->
                        </select>
                        </div>
                        <div class="form-group">
                            <label for="apellidoP">Puesto</label>
                            <input type="text" class="form-control" id="puesto" name="puesto" required>
                        </div>
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
                            <input type="text" class="form-control" id="apellidoM" name="apellidoM">
                        </div>
                        <div class="form-group">
                            <label for="nick">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nick" name="nick" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="times" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button"  class="btn btn-primary" id="saveAdmin">Guardar</button>
            </div>
        </div>
    </div>
</div>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tabla de Usuarios (InPlant)</h5>
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
                        <table id="adminTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Nombre de Usuario</th>
                                    <th>Planta</th>
                                    <th>Estatus</th>
                                    <th>Puesto</th>
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

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container {
            z-index: 1050 !important; /* Asegúrate de que sea mayor que el del modal (Bootstrap modals usan 1040 o 1050) */
        }
        
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
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#adminTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get-usuarios') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                },
                    columns: [
                    { data: 'NombreCompleto', name: 'NombreCompleto' },
                    { data: 'NombreUsuario', name: 'NombreUsuario' },
                    { data: 'Nombre_Planta', name: 'NombrePlanta' },
                    {
                        data: 'estatus_icon',
                        name: 'estatus_icon',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Generar el icono dependiendo del estatus
                            let iconClass = row.Txt_Estatus === 'Alta' ? 'fa-toggle-on fa-2x text-success' : 'fa-toggle-off fa-2x text-danger';
                            let nuevoEstatus = row.Txt_Estatus === 'Alta' ? 'Baja' : 'Alta';
                            
                            // Retornar el HTML del icono con el evento onclick
                            return `
                                <i id="estatus-icon-${row.id}" 
                                class="fas ${iconClass}" 
                                style="cursor: pointer;"
                                onclick="toggleEstatus(${row.id}, '${nuevoEstatus}')"></i>
                            `;
                        }
                    },
                    { data: 'Txt_Puesto', name: 'Txt_Puesto' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Agregar botón de eliminar
                            return `
                                <button class="btn btn-danger btn-sm" 
                                onclick="deleteAdmin(${row.id})" 
                                title="Eliminar">
                                    <i class="fas fa-trash fa-2x"></i>
                                </button>
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
                    // Agrega clase a la columna de Alta
                    $('td', row).eq(6).addClass('alta');
                    $('td', row).eq(7).addClass('alta'); // Usuario Alta
                    
                    // Agrega clase a la columna de Modificación
                    $('td', row).eq(8).addClass('modificacion');
                    $('td', row).eq(9).addClass('modificacion'); // Usuario Modificación
                    
                    // Agrega clase a la columna de Baja
                    $('td', row).eq(10).addClass('baja');
                    $('td', row).eq(11).addClass('baja'); // Usuario Baja
                },
                order: [[0, 'asc']],
                responsive: true,
                scrollX: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                }
            });

             // Inicializa Select2
             $('#addAdminModal').on('shown.bs.modal', function () {
                $('#planta').select2({
                    placeholder: 'Seleccione una planta',
                    allowClear: true,
                    dropdownParent: $('#addAdminModal') // Asegura que Select2 se asocia con el modal
                });
            });

    // Cargar plantas desde el servidor
    $.ajax({
        url: '/getPlantas', // Cambia esta URL a la que retorna tus plantas
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(data) {
            $.each(data, function(index, planta) {
                $('#planta').append(new Option(planta.Txt_Nombre_Planta, planta.Id_Planta));
            });
            // Refresca select2 para que reconozca las nuevas opciones
            $('#planta').select2('destroy').select2({
                placeholder: 'Seleccione una planta',
                allowClear: true
            });
        },
        error: function(xhr) {
            console.error('Error al cargar las plantas:', xhr);
        }
    });


         // Inicialmente, ocultamos las secciones de formularios
    $('#plantCreationSection').hide();
    $('#adminCreationSection').hide();
    $('#times').hide();
    $('#save').hide();

    // Cambiar a la sección de crear planta
    $('#createPlantBtn, #addplanta').on('click', function() {
        $('#adminCreationSection').hide();
        $('#plantCreationSection').show();
        $('#times').show();
        $('#save').show();
    });

    // Cambiar a la sección de agregar administrador
    $('#existingPlantBtn, #addusuario').on('click', function() {
        $('#plantCreationSection').hide();
        $('#adminCreationSection').show();
        $('#times').show();
        $('#save').show();
    });

    // Guardar administrador y planta
$('#saveAdmin').on('click', function() {
    if ($('#adminCreationSection').is(':visible')) {
        // Si estamos en la sección de agregar administrador
        var formData = $('#addAdminForm').serialize();

        // Validar que las contraseñas coincidan
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
            return; // Detener la ejecución
        }

        $.ajax({
            url: '/guardar-usuario', // Cambia la URL al endpoint correspondiente en tu controlador
            method: 'POST',
            data: formData,
            success: function(response) {
                alert('Usuario creado exitosamente.');
                $('#addAdminModal').modal('hide');
                $('#addAdminForm')[0].reset();
                 // Recargar la tabla de administradores
                 $('#adminTable').DataTable().ajax.reload(null, false); // Recargar sin reiniciar la paginación
            },
            error: function(xhr) {
                // Verificar si el servidor ha devuelto una respuesta JSON válida
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        alert(value[0]); // Muestra el primer error en la alerta
                    });
                } else {
                    // Si no hay respuesta JSON válida, mostrar el mensaje de error genérico
                    alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
                    console.log(xhr.responseText); // Muestra la respuesta completa en la consola para depurar
                }
            }
        });
    } else {
        // Si estamos en la sección de crear planta
        var plantData = $('#createPlantForm').serialize();

        $.ajax({
            url: '/guardar-planta', // Cambia la URL al endpoint correspondiente en tu controlador
            method: 'POST',
            data: plantData,
            success: function(response) {
                alert('Planta creada exitosamente.');
                $('#addAdminModal').modal('hide');
                $('#createPlantForm')[0].reset();
                // Agregar la nueva planta al select de planta
                var newOption = new Option(response.nombre_planta, response.id_planta, true, true);
                $('#planta').append(newOption).trigger('change'); // Actualiza select2 con la nueva opción
            },
            error: function(xhr) {
                // Verificar si el servidor ha devuelto una respuesta JSON válida
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        alert(value[0]); // Muestra el primer error en la alerta
                    });
                } else {
                    // Si no hay respuesta JSON válida, mostrar el mensaje de error genérico
                    alert('Error: ' + xhr.status + ' - ' + xhr.statusText);
                    console.log(xhr.responseText); // Muestra la respuesta completa en la consola para depurar
                }
            }
        });
    }
});


            
        });

        

        // Función para eliminar un administrador
        function deleteAdmin(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                $.ajax({
                    url: `/usuario/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Usuario eliminado exitosamente');
                        $('#adminTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let mensaje = xhr.responseJSON?.message || 'Error desconocido';
                        alert('Error al eliminar usuario: ' + mensaje);
                    }
                });
            }
        }
    </script>
    <script>
        function toggleEstatus(id, nuevoEstatus) {
                $.ajax({
                    url: '{{ url("/usuario/estatus") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        nuevoEstatus: nuevoEstatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // Recargar el DataTable después de actualizar el estatus
                            $('#adminTable').DataTable().ajax.reload();
                        } else {
                            console.error('Error al actualizar el estatus: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al actualizar el estatus: ' + xhr.responseText);
                    }
                });
            }
    </script>
@stop
