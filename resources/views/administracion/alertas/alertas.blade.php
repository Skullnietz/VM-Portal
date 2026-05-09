@extends('adminlte::page')

@section('title', 'Alertas')

@section('content_header')
<h1>Gestión de Alertas</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Alertas</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-success btn-sm" id="btnCrearAlerta">
                <i class="fas fa-plus"></i> Crear Alerta
            </button>
        </div>
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

<!-- Modal Crear/Editar Alerta -->
<div class="modal fade" id="modalAlerta" tabindex="-1" role="dialog" aria-labelledby="modalAlertaLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAlertaLabel">Gestión de Alerta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAlerta" novalidate>
                @csrf
                <input type="hidden" id="alertaId" name="id">
                <div class="modal-body">
                    <div class="form-group" id="divUsuario">
                        <label for="Id_Usuario">Usuario</label>
                        <select class="form-control" id="Id_Usuario" name="Id_Usuario" required>
                            <option value="">Seleccione un usuario</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Frecuencia">Frecuencia</label>
                        <select class="form-control" id="Frecuencia" name="Frecuencia" required>
                            <option value="diario">Diario</option>
                            <option value="semanal">Semanal</option>
                            <option value="mensual">Mensual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Email">Email</label>
                        <input type="email" class="form-control" id="Email" name="Email" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="Recibir_Notificaciones"
                                name="Recibir_Notificaciones" value="1">
                            <label class="custom-control-label" for="Recibir_Notificaciones">Recibir
                                Notificaciones</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
{{-- Agrega aquí estilos personalizados si es necesario --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@stop

@section('js')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        var table = $('#alertasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/allalertas",
                type: 'POST',
                data: function (d) {
                    d._token = '{{ csrf_token() }}';  // Asegurando que CSRF se pase correctamente
                    return d;
                }
            },
            columns: [
                { data: 'Id', name: 'Id' },
                {
                    data: 'NombreCompleto', name: 'NombreCompleto', render: function (data, type, row) {
                        return data + ' (' + row.Nick_Usuario + ')';
                    }
                },
                { data: 'Txt_Nombre_Planta', name: 'Txt_Nombre_Planta' },
                { data: 'Frecuencia', name: 'Frecuencia' },
                { data: 'Email', name: 'Email' },
                {
                    data: 'Recibir_Notificaciones', name: 'Recibir_Notificaciones', render: function (data, type, row) {
                        return data == '1' ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>';
                    }
                },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                {
                    data: 'action', name: 'action', orderable: false, searchable: false, render: function (data, type, row) {
                        return '<button class="btn btn-primary btn-sm btn-editar" data-id="' + row.Id + '"><i class="fas fa-edit"></i> Editar</button> ' +
                            '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' + row.Id + '"><i class="fas fa-trash"></i> Eliminar</button>';
                    }
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            }
        });

        // Cargar usuarios para el select
        function loadUsers() {
            $.ajax({
                url: "{{ route('alertas.users') }}",
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (data) {
                    var select = $('#Id_Usuario');
                    select.empty();
                    select.append('<option value="">Seleccione un usuario</option>');
                    $.each(data, function (key, value) {
                        select.append('<option value="' + value.Id_Usuario + '">' + value.NombreCompleto + '</option>');
                    });
                }
            });
        }

        loadUsers();
        $('#Id_Usuario').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modalAlerta')
        });

        // Abrir modal para crear
        $('#btnCrearAlerta').click(function () {
            $('#formAlerta')[0].reset();
            $('#alertaId').val('');
            $('#modalAlertaLabel').text('Crear Alerta');
            $('#divUsuario').show(); // Mostrar selector de usuario
            $('#Id_Usuario').val('').trigger('change');
            $('#modalAlerta').modal('show');
        });

        // Abrir modal para editar
        $('#alertasTable tbody').on('click', '.btn-editar', function () {
            var id = $(this).data('id');
            $.get('/admin/alertas/' + id, function (data) {
                $('#modalAlertaLabel').text('Editar Alerta');
                $('#alertaId').val(data.Id);
                $('#divUsuario').hide(); // Ocultar selector de usuario al editar (generalmente no se cambia el usuario)
                $('#Frecuencia').val(data.Frecuencia);
                $('#Email').val(data.Email);
                $('#Recibir_Notificaciones').prop('checked', data.Recibir_Notificaciones == 1);
                $('#modalAlerta').modal('show');
            });
        });

        // Guardar alerta
        $('#formAlerta').submit(function (e) {
            e.preventDefault();
            console.log("Formulario enviado"); // Debug
            var id = $('#alertaId').val();
            var url = id ? '/admin/alertas/update/' + id : "{{ route('alertas.store') }}";
            console.log("URL: " + url); // Debug
            var formData = $(this).serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (response) {
                    $('#modalAlerta').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Éxito', response.success, 'success');
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    if (errors) {
                        $.each(errors, function (key, value) {
                            errorMessage += value[0] + '\n';
                        });
                    } else {
                        errorMessage = xhr.responseJSON.error || 'Error desconocido';
                    }
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        });

        // Eliminar alerta
        $('#alertasTable tbody').on('click', '.btn-eliminar', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/alertas/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            table.ajax.reload();
                            Swal.fire('Eliminado!', response.success, 'success');
                        }
                    });
                }
            })
        });
    });
</script>
@stop