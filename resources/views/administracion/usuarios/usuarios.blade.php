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
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                Agregar Usuario (InPlant)&nbsp;&nbsp;&nbsp;<i class="fas fa-user-plus"></i>
            </button>
        </div>
    </div>
</div>
@stop
<!-- Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Agregar Administrador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm">
                @csrf <!-- Token CSRF -->
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveAdmin">Guardar Administrador</button>
            </div>
        </div>
    </div>
</div>
@section('content')
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
    <!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#adminTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('get-usuarios') }}',
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

            
        });

        // Función para eliminar un administrador
        function deleteAdmin(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este administrador?')) {
                $.ajax({
                    url: `/administrador/${id}`, // Asegúrate de que esta URL sea correcta
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // Incluyendo el token CSRF
                    },
                    success: function(response) {
                        alert('Administrador eliminado exitosamente');
                        $('#adminTable').DataTable().ajax.reload(); // Recargar la tabla
                    },
                    error: function(xhr) {
                        alert('Error al eliminar administrador: ' + xhr.responseJSON.message);
                    }
                });
            }
        }
    </script>
    <script>
        function toggleEstatus(id, nuevoEstatus) {
                $.ajax({
                    url: '{{ url("/administrador/estatus") }}',
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
    <script>
    $(document).ready(function() {
        $('#saveAdmin').on('click', function() {
            // Validar que las contraseñas coincidan
            var password = $('#password').val();
            var confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                return; // Detener la ejecución
            }

            var formData = $('#addAdminForm').serialize();

            $.ajax({
                url: '/administrador/add', // Cambia esto por la ruta de tu controlador
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Manejar la respuesta exitosa aquí
                    alert('Administrador agregado con éxito');
                    $('#addAdminModal').modal('hide');
                    // Opcionalmente, puedes actualizar el datatable aquí
                },
                error: function(xhr) {
                // Manejar errores aquí
                let errorMessage = 'Error al agregar administrador.';

                // Comprobar si hay errores de validación
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Recopilar todos los mensajes de error
                    let errorDetails = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorDetails += value.join(', ') + '\n'; // Unir los mensajes de error
                    });
                    errorMessage += '\nDetalles: \n' + errorDetails;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Si hay un mensaje general
                    errorMessage += '\n' + xhr.responseJSON.message;
                } else {
                    // Mensaje de error genérico
                    errorMessage += '\nError desconocido.';
                }

                alert(errorMessage); // Mostrar el mensaje de error detallado
            }
            });
        });
    });
</script>
@stop
