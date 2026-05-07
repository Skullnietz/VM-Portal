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
                    <th>Tipo Reporte</th>
                    <th>Último Envío</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Crear/Editar Alerta -->
<div class="modal fade" id="modalAlerta" tabindex="-1" role="dialog" aria-labelledby="modalAlertaLabel" aria-hidden="true">
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
                        <label for="Plantilla">Tipo de Reporte</label>
                        <select class="form-control" id="Plantilla" name="Plantilla">
                            <option value="consumo_general">Reporte General (estilo Audi Puebla)</option>
                            {{-- Agregar más opciones aquí cuando se registren nuevas plantillas --}}
                        </select>
                        <small class="form-text text-muted">Define el formato del Excel que se enviará.</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="Recibir_Notificaciones"
                                name="Recibir_Notificaciones" value="1">
                            <label class="custom-control-label" for="Recibir_Notificaciones">Recibir Notificaciones</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="Activo" name="Activo" value="1" checked>
                            <label class="custom-control-label" for="Activo">Alerta Activa</label>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
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
                d._token = '{{ csrf_token() }}';
                return d;
            }
        },
        columns: [
            { data: 'Id', name: 'Id' },
            {
                data: 'NombreCompleto', name: 'NombreCompleto',
                render: function (data, type, row) {
                    return data + ' (' + row.Nick_Usuario + ')';
                }
            },
            { data: 'Txt_Nombre_Planta', name: 'Txt_Nombre_Planta' },
            { data: 'Frecuencia', name: 'Frecuencia' },
            { data: 'Email', name: 'Email' },
            {
                data: 'Tipo_Reporte', name: 'Tipo_Reporte',
                render: function (data) {
                    return '<span class="badge badge-info">' + (data || 'consumo_general') + '</span>';
                }
            },
            {
                data: 'Ultimo_Envio', name: 'Ultimo_Envio',
                render: function (data) {
                    return data ? data : '<span class="text-muted">—</span>';
                }
            },
            {
                data: 'Activo', name: 'Activo',
                render: function (data) {
                    return data == 1
                        ? '<span class="badge badge-success">Sí</span>'
                        : '<span class="badge badge-secondary">No</span>';
                }
            },
            {
                data: 'action', name: 'action', orderable: false, searchable: false,
                render: function (data, type, row) {
                    return '<button class="btn btn-primary btn-sm btn-editar mr-1" data-id="' + row.Id + '"><i class="fas fa-edit"></i></button>'
                         + '<button class="btn btn-success btn-sm btn-enviar mr-1" data-id="' + row.Id + '" title="Enviar Ahora"><i class="fas fa-paper-plane"></i></button>'
                         + '<button class="btn btn-danger btn-sm btn-eliminar" data-id="' + row.Id + '"><i class="fas fa-trash"></i></button>';
                }
            }
        ],
        language: { url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json" }
    });

    function loadUsers() {
        $.ajax({
            url: "{{ route('alertas.users') }}",
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (data) {
                var select = $('#Id_Usuario');
                select.empty().append('<option value="">Seleccione un usuario</option>');
                $.each(data, function (key, value) {
                    select.append('<option value="' + value.Id_Usuario + '">' + value.NombreCompleto + '</option>');
                });
            }
        });
    }

    loadUsers();
    $('#Id_Usuario').select2({ theme: 'bootstrap4', dropdownParent: $('#modalAlerta') });

    // Abrir modal — crear
    $('#btnCrearAlerta').click(function () {
        $('#formAlerta')[0].reset();
        $('#alertaId').val('');
        $('#modalAlertaLabel').text('Crear Alerta');
        $('#divUsuario').show();
        $('#Id_Usuario').val('').trigger('change');
        $('#Activo').prop('checked', true);
        $('#modalAlerta').modal('show');
    });

    // Abrir modal — editar
    $('#alertasTable tbody').on('click', '.btn-editar', function () {
        var id = $(this).data('id');
        $.get('/admin/alertas/' + id, function (data) {
            $('#modalAlertaLabel').text('Editar Alerta');
            $('#alertaId').val(data.Id);
            $('#divUsuario').hide();
            $('#Frecuencia').val(data.Frecuencia);
            $('#Email').val(data.Email);
            $('#Plantilla').val(data.Plantilla || 'consumo_general');
            $('#Recibir_Notificaciones').prop('checked', data.Recibir_Notificaciones == 1);
            $('#Activo').prop('checked', data.Activo == 1);
            $('#modalAlerta').modal('show');
        });
    });

    // Enviar ahora
    $('#alertasTable tbody').on('click', '.btn-enviar', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: '¿Enviar reporte ahora?',
            text: 'Se generará y enviará el reporte de inmediato para esta configuración.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/alertas/' + id + '/enviar-ahora',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        table.ajax.reload();
                        Swal.fire('Enviado', response.success, 'success');
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.error || 'Error al enviar.', 'error');
                    }
                });
            }
        });
    });

    // Guardar (crear / actualizar)
    $('#formAlerta').submit(function (e) {
        e.preventDefault();
        var id  = $('#alertaId').val();
        var url = id ? '/admin/alertas/update/' + id : "{{ route('alertas.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                $('#modalAlerta').modal('hide');
                table.ajax.reload();
                Swal.fire('Éxito', response.success, 'success');
            },
            error: function (xhr) {
                var errors = xhr.responseJSON?.errors;
                var msg = errors
                    ? Object.values(errors).map(v => v[0]).join('\n')
                    : (xhr.responseJSON?.error || 'Error desconocido');
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Eliminar
    $('#alertasTable tbody').on('click', '.btn-eliminar', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/alertas/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        table.ajax.reload();
                        Swal.fire('Eliminado', response.success, 'success');
                    }
                });
            }
        });
    });
});
</script>
@stop
