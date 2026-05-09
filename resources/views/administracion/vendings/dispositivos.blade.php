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
        <div class="col-md-3 col-3 ml-auto text-right">
            <button class="btn btn-add btn-modern shadow" data-toggle="modal" data-target="#addModal">
                <i class="fas fa-plus-circle mr-1"></i> Añadir Dispositivo
            </button>
        </div>


    </div>
</div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-dark"><i class="fas fa-microchip mr-2 text-primary"></i> Tabla de
                        Dispositivos</h5>
                    <div class="card-tools ml-auto">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover table-striped display responsive nowrap" style="width:100%"
                            id="dispositivosTable">
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
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="addForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle text-success mr-2"></i> Añadir Dispositivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger d-none" id="addFormErrors"></div>
                        <div class="form-group mb-3">
                            <label for="Txt_Serie_Dispositivo" class="font-weight-bold">Serie del Dispositivo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-modern"><i
                                            class="fas fa-barcode"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-modern input-with-icon"
                                    id="Txt_Serie_Dispositivo" name="Txt_Serie_Dispositivo"
                                    placeholder="Ingrese el número de serie" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="Txt_Estatus" class="font-weight-bold">Estatus</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-modern"><i
                                            class="fas fa-toggle-on"></i></span>
                                </div>
                                <select class="form-control form-control-modern input-with-icon" id="Txt_Estatus"
                                    name="Txt_Estatus" required>
                                    <option value="Alta">Alta (Activo)</option>
                                    <option value="Baja">Baja (Inactivo)</option>
                                </select>
                            </div>
                        </div>
                        <!-- Maquina Selection (if needed in add) -->
                        <div class="form-group mb-3">
                            <label for="selectMaquina" class="font-weight-bold">Asignar a Máquina (Opcional)</label>
                            <select class="form-control select2" id="selectMaquina" name="Id_Maquina" style="width: 100%;">
                                <option></option> <!-- Placeholder -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-add btn-modern"><i class="fas fa-save mr-1"></i> Guardar
                            Dispositivo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar Dispositivo -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="editForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit text-info mr-2"></i> Editar Dispositivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger d-none" id="editFormErrors"></div> <!-- Contenedor de errores -->
                        <input type="hidden" id="editId" name="Id_Dispositivo">

                        <div class="form-group mb-3">
                            <label for="editSerie" class="font-weight-bold">Serie del Dispositivo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-modern"><i
                                            class="fas fa-barcode"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-modern input-with-icon" id="editSerie"
                                    name="Txt_Serie_Dispositivo" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="editEstatus" class="font-weight-bold">Estatus</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-modern"><i
                                            class="fas fa-toggle-on"></i></span>
                                </div>
                                <select class="form-control form-control-modern input-with-icon" id="editEstatus"
                                    name="Txt_Estatus" required>
                                    <option value="Alta">Alta</option>
                                    <option value="Baja">Baja</option>
                                </select>
                            </div>
                        </div>
                        <!-- Maquina Selection (Edit) -->
                        <div class="form-group mb-3">
                            <label for="editSelectMaquina" class="font-weight-bold">Máquina Asignada</label>
                            <select class="form-control select2" id="editSelectMaquina" name="Id_Maquina"
                                style="width: 100%;">
                                <!-- Loaded via AJAX -->
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-modern"><i class="fas fa-sync-alt mr-1"></i>
                            Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('right-sidebar')
@stop

@section('css')
<!-- DataTables & Plugins -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f4f6f9;
        color: #333;
    }

    /* Card Styling */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s;
    }

    .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem;
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
    }

    .card-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.25rem;
    }

    /* Modern Table Styling */
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        margin-top: 1rem;
    }

    table.dataTable {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0;
    }

    table.dataTable thead th {
        background: #343a40;
        /* Dark premium header */
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }

    table.dataTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
        font-size: 0.95rem;
    }

    table.dataTable tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
        transition: all 0.2s ease-in-out;
    }

    /* Status Badges */
    .badge-modern {
        padding: 0.5em 0.8em;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85em;
    }

    .badge-success-modern {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .badge-danger-modern {
        background-color: #f8d7da;
        color: #842029;
    }

    /* Buttons */
    .btn-modern {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-add {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        border: none;
        color: white;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 700;
        color: #2c3e50;
    }

    .form-control-modern {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem; /* Reduced padding */
        font-size: 0.9rem; /* Smaller font */
        height: auto;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control-modern:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    .input-group-text-modern {
        background-color: white;
        border-right: none;
        border-radius: 8px 0 0 8px;
        padding: 0.5rem 0.75rem; /* Match input padding */
        font-size: 0.9rem;
    }

    .input-with-icon {
        border-left: none;
        border-radius: 0 8px 8px 0;
    }

    /* Select2 Customization to match form-control-modern */
    .select2-container .select2-selection--single {
        height: 38px !important; /* Fixed height to match inputs */
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        padding: 0.3rem 0.5rem; /* Align text */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        font-size: 0.9rem;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@stop

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
    function goBack() {
        window.history.back();
    }
</script>
<script>
    // Inicializar Select2 en ambos modales
    // Configuración común para Select2 AJAX
    const select2AjaxConfig = {
        url: "{{ route('maquinas.list') }}",
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
    };

    // Inicializar Select2 para el modal de Añadir
    $('#selectMaquina').select2({
        dropdownParent: $('#addModal'),
        placeholder: "Selecciona una máquina",
        width: '100%',
        ajax: select2AjaxConfig
    });

    // Inicializar Select2 para el modal de Editar
    $('#editSelectMaquina').select2({
        dropdownParent: $('#editModal'),
        placeholder: "Selecciona una máquina",
        width: '100%',
        ajax: select2AjaxConfig
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
            responsive: true,
            autoWidth: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
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
                { data: 'Txt_Serie_Dispositivo', name: 'Txt_Serie_Dispositivo', render: function (data) { return '<b>' + data + '</b>'; } },
                {
                    data: 'Txt_Estatus', name: 'Txt_Estatus', render: function (data) {
                        if (data === 'Alta') return '<span class="badge badge-success-modern">Alta</span>';
                        return '<span class="badge badge-danger-modern">Baja</span>';
                    }
                },
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