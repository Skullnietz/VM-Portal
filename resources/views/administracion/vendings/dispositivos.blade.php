@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Dispositivos'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Dispositivos') }}</h4>
            </div>
            <div class="col-md-3 col-3 ml-auto">
            <button class="btn btn-success" data-toggle="modal" data-target="#addModal">Añadir Dispositivo <i class="fas fa-fw  fa-tablet"></i></button>
            </div>


        </div>
    </div>
@stop

@section('content')
<div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tabla de Dispositivos</h5>
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
                        <div class="container mt-4">
                            
                        <table class="table table-striped" id="dispositivosTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Serie</th>
                                    <th>Estatus</th>
                                    <th>ID Máquina</th>
                                    <th>Nombre Máquina</th>
                                    <th>Creado Por</th>
                                    <th>Modificado Por</th>
                                    <th>Fecha Alta</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                        </table>
                        </div>
                        </div> 
                        </div>
                        </div>
                        </div>

<!-- Modal: Añadir Dispositivo -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="addForm">
        @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Dispositivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="Txt_Serie_Dispositivo">Serie del Dispositivo</label>
                        <input type="text" class="form-control" id="Txt_Serie_Dispositivo" name="Txt_Serie_Dispositivo" required>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Estatus">Estatus</label>
                        <select class="form-control" id="Txt_Estatus" name="Txt_Estatus" required>
                            <option value="Alta">Alta</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal: Editar Dispositivo -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Dispositivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="editFormErrors"></div> <!-- Contenedor de errores -->
                    <input type="hidden" id="editId" name="Id_Dispositivo">
                    <div class="form-group">
                        <label for="editSerie">Serie del Dispositivo</label>
                        <input type="text" class="form-control" id="editSerie" name="Txt_Serie_Dispositivo" required>
                    </div>
                    <div class="form-group">
                        <label for="editEstatus">Estatus</label>
                        <select class="form-control" id="editEstatus" name="Txt_Estatus" required>
                            <option value="Alta">Alta</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@section('right-sidebar')
@stop

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
@stop

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
    function goBack() {
      window.history.back();
    }
</script>
<script>
    // Inicializar Select2 en ambos modales
$('.select2').select2({
    placeholder: "Selecciona una máquina",
    ajax: {
        ajax: {
        url: "{{ route('maquinas.list') }}", // Ruta para obtener las máquinas
        dataType: 'json',
        delay: 250,
        type: 'POST',
        data: function (params) {
            return {
                _token: $('meta[name="csrf-token"]').attr('content'),
                search: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data.map(function (item) {
                    return { id: item.Id_Maquina, text: item.Txt_Nombre };
                })
            };
        },
        cache: true
    }
    }
});

// Cargar máquina en el modal de edición
$(document).on('click', '.edit-btn', function () {
    let id = $(this).data('id');

    // Obtener datos del dispositivo
    $.get("{{ url('dispositivos') }}/" + id, function (data) {
        $('#editId').val(data.Id_Dispositivo);
        $('#editSerie').val(data.Txt_Serie_Dispositivo);
        $('#editEstatus').val(data.Txt_Estatus);

        // Establecer la máquina seleccionada en Select2
        let selectMaquina = $('#editSelectMaquina');
        if (data.Id_Maquina) {
            let option = new Option(data.Maquina_Nombre, data.Id_Maquina, true, true);
            selectMaquina.append(option).trigger('change');
        } else {
            selectMaquina.val(null).trigger('change');
        }

        $('#editModal').modal('show');
    });
});
</script>
<script>
    $(document).ready(function () {
        // Inicializar DataTable
        $('#dispositivosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dispositivos.get') }}",
            type: 'POST',
            data: function (d) {
                d._token = $('meta[name="csrf-token"]').attr('content');
            }
        },
        columns: [
            { data: 'Id_Dispositivo', name: 'Id_Dispositivo' },
            { data: 'Txt_Serie_Dispositivo', name: 'Txt_Serie_Dispositivo' },
            { data: 'Txt_Estatus', name: 'Txt_Estatus' },
            { data: 'Id_Maquina', name: 'Id_Maquina' },
            { data: 'Maquina_Nombre', name: 'Maquina_Nombre' },
            { data: 'Creado_Por', name: 'Creado_Por' },
            { data: 'Modificado_Por', name: 'Modificado_Por' },
            { data: 'Fecha_Alta', name: 'Fecha_Alta' },
            { data: 'Opciones', name: 'Opciones', orderable: false, searchable: false },
        ],
    });

        // Formulario: Añadir
        $('#addForm').submit(function (e) {
            e.preventDefault();
            let data = $(this).serialize();
            $.post("{{ route('dispositivos.store') }}", data, function () {
                $('#addModal').modal('hide');
                table.ajax.reload();
            });
        });

        // Formulario: Editar
        $('#editForm').submit(function (e) {
            e.preventDefault();
            let id = $('#editId').val();
            let data = $(this).serialize();
            $.post("{{ url('dispositivos/update') }}/" + id, data, function () {
                $('#editModal').modal('hide');
                table.ajax.reload();
            });
        });

        

           // Enviar datos del formulario de Añadir
            $('#addForm').submit(function (e) {
                e.preventDefault();

                let data = $(this).serializeArray();
                let maquinaId = $('#selectMaquina').val();

                // Si no hay máquina seleccionada, asignar NULL
                data.push({ name: 'Id_Maquina', value: maquinaId ? maquinaId : null });

                $.ajax({
                    url: "{{ route('dispositivos.store') }}",
                    type: "POST",
                    data: data,
                    success: function (response) {
                        alert(response.success);
                        $('#addModal').modal('hide'); // Cerrar el modal
                        $('#addFormErrors').addClass('d-none').html(''); // Limpiar errores
                        $('#dispositivosTable').DataTable().ajax.reload(); // Recargar la tabla
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = '<ul>';
                            for (let field in errors) {
                                errorMessages += '<li>' + errors[field][0] + '</li>';
                            }
                            errorMessages += '</ul>';
                            $('#addFormErrors').removeClass('d-none').html(errorMessages);
                        } else {
                            alert('Error al guardar el dispositivo.');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-btn', function () {
                let id = $(this).data('id'); // Obtener el ID del dispositivo

                if (confirm('¿Estás seguro de que deseas eliminar este dispositivo?')) {
                    $.ajax({
                        url: "{{ url('dispositivos/destroy') }}/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Token CSRF
                        },
                        success: function (response) {
                            alert(response.success);
                            $('#dispositivosTable').DataTable().ajax.reload(); // Recargar la tabla
                        },
                        error: function (xhr) {
                            alert('Error: ' + xhr.responseJSON.error);
                        }
                    });
                }
            });

            // Botón para abrir el modal de edición
$(document).on('click', '.edit-btn', function () {
    let id = $(this).data('id');

    // Obtener los datos del dispositivo para editar
    $.get("{{ url('dispositivos') }}/" + id, function (data) {
        $('#editId').val(data.Id_Dispositivo);
        $('#editSerie').val(data.Txt_Serie_Dispositivo);
        $('#editEstatus').val(data.Txt_Estatus);
        $('#editModal').modal('show'); // Mostrar el modal
    }).fail(function () {
        alert('Error al cargar los datos del dispositivo.');
    });
});

// Enviar datos del formulario de edición
// Enviar datos del formulario de Edición
$('#editForm').submit(function (e) {
    e.preventDefault();

    let id = $('#editId').val();
    let data = $(this).serializeArray();
    let maquinaId = $('#editSelectMaquina').val();

    // Si no hay máquina seleccionada, asignar NULL
    data.push({ name: 'Id_Maquina', value: maquinaId ? maquinaId : null });

    $.ajax({
        url: "{{ url('dispositivos/update') }}/" + id,
        type: "POST",
        data: data,
        success: function (response) {
            alert(response.success);
            $('#editModal').modal('hide'); // Cerrar el modal
            $('#editFormErrors').addClass('d-none').html(''); // Limpiar errores
            $('#dispositivosTable').DataTable().ajax.reload(); // Recargar la tabla
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = '<ul>';
                for (let field in errors) {
                    errorMessages += '<li>' + errors[field][0] + '</li>';
                }
                errorMessages += '</ul>';
                $('#editFormErrors').removeClass('d-none').html(errorMessages);
            } else {
                alert('Error al actualizar el dispositivo.');
            }
        }
    });
});


    });
</script>
@stop
