@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Empleados'))

@section('content_header')
<div class="container-fluid">
    <div class="row">
        <!-- Columna izquierda: en móviles ocupa todo el ancho -->
        <div class="col-12 col-md-2 d-flex align-items-center mb-2 mb-md-0">
            <h4 class="mb-0">
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;{{ __('Empleados') }}
            </h4>
        </div>
        <!-- Columna derecha: en móviles ocupa todo el ancho -->
        <div class="col-12 col-md-10 d-flex justify-content-end align-items-center">
            <div class="btn-group" role="group" aria-label="Basic example">
                <a type="button" href="{{ url('export-csv-employees') }}" class="btn btn-secondary">
                    Descarga .CSV &nbsp;&nbsp;&nbsp;<i class="fas fa-file-csv"></i> <i class="fas fa-download"></i>
                </a>
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#uploadCsvModal">
                    Subida .CSV &nbsp;&nbsp;&nbsp;<i class="fas fa-file-csv"></i> <i class="fas fa-cloud-upload-alt"></i>
                </button>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                    Agregar Empleado &nbsp;&nbsp;&nbsp;<i class="fas fa-user-plus"></i>
                </button>
                <a href="{{ url('export-excel-employees') }}" type="button" class="btn btn-success">
                    Reporte &nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Administrador de Empleados</h5>
                        <div class="card-tools">
                        <div class="btn-group">
                        <label for="estatusFilter" class="mr-2 mb-0">Filtrar por estatus:</label>
                        <span id="estatusIndicator" class="estatus-light bg-success"></span>
                        <select id="estatusFilter" class="form-control mr-2" style="width: 200px; margin-left: 5px;">
                            <option value="Alta" selected>Altas</option>
                            <option value="Baja">Bajas</option>
                            <option value="">Todos</option>
                        </select>
                        
                            </div>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="empleados-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No. empleado</th>
                                    <th>NIP</th>
                                    <th>Tarjeta</th>
                                    <th>Nombre</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Área</th>
                                    <th>Permisos producto</th>
                                    <th>Editar</th>
                                    <th>Borrar</th>
                                    <th>Estatus</th>
                                    <th>Última modificación</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Campo oculto para el ID -->
                    <input type="hidden" id="editId" name="id">
                    <form id="editForm">
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-key"></i>&nbsp;&nbsp;| NIP
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="nip" name="nip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-address-card"></i>&nbsp;&nbsp;| ID Tarjeta
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="notarjeta" name="notarjeta">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-user"></i>&nbsp;&nbsp;| Nombre
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Paterno
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="apaterno" name="apaterno" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Materno
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="amaterno" name="amaterno">
                            </div>
                        </div>
                        <div class="form-group"> 
                            <label for="area">
                                <i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Área &nbsp;| &nbsp;&nbsp;Permisos de Producto
                            </label>
                            <select class="form-control" id="area" name="area" required>
                                <!-- Opciones se llenarán con JavaScript -->
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload CSV Modal -->
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" role="dialog" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadCsvModalLabel">Subir archivo CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url('import-csv-employees') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="csvFile">Selecciona el archivo CSV</label>
                            <input type="file" class="form-control" id="csvFile" name="csv_file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Subir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para agregar empleado -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Agregar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addEmployeeForm">
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-id-card-alt"></i>&nbsp;&nbsp;| N° Empleado
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="no_empleado" name="no_empleado" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-key"></i>&nbsp;&nbsp;| NIP
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="nip" name="nip">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-address-card"></i>&nbsp;&nbsp;| ID Tarjeta
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="no_tarjeta" name="no_tarjeta">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-user"></i>&nbsp;&nbsp;| Nombre
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Paterno
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="apaterno" name="apaterno" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed">
                                        <i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Materno
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="amaterno" name="amaterno">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="area">
                                <i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Área &nbsp;| &nbsp;&nbsp;Permisos de Producto
                            </label>
                            <select id="addArea" name="area" required></select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .estatus-light {
        display: inline-block;
        width: 15px;
        height: 15px;
        margin-top: 5px;
        border-radius: 50%;
        transition: background-color 0.3s;
        border: 1px solid #ccc;
    }

    .bg-success {
        background-color: #28a745;
        animation: blink-success 1s infinite;
    }

    .bg-danger {
        background-color: #dc3545;
        animation: blink-danger 1s infinite;
    }

    .bg-secondary {
        background-color: #6c757d;
        animation: none;
    }

    @keyframes blink-success {
        0%, 100% { background-color: #28a745; }
        50% { background-color: #1e7e34; }
    }

    @keyframes blink-danger {
        0%, 100% { background-color: #dc3545; }
        50% { background-color: #a71d2a; }
    }
    .input-group-text-fixed {
        min-width: 160px;
        text-align: center;
    }
    /* Ajustes para dispositivos móviles */
    @media (max-width: 576px) {
        .input-group-text-fixed {
            min-width: 120px;
            font-size: 0.9rem;
        }
        .btn-group a, .btn-group button {
            margin-bottom: 5px;
        }
        /* Asegura que los inputs de DataTables se adapten al ancho */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            width: 100% !important;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    $(document).ready(function () {
        let areaSelect = document.getElementById("addArea");
        let areaChoices;

        function cargarAreasParaAgregar() {
            $.ajax({
                url: '{!! route('areas.data') !!}',
                method: 'GET',
                success: function (data) {
                    areaSelect.innerHTML = ''; // Limpiar anteriores

                    let options = data.map(area => ({
                        value: area.Id_Area,
                        label: area.Txt_Nombre
                    }));

                    if (areaChoices) {
                        areaChoices.destroy();
                    }

                    areaChoices = new Choices(areaSelect, {
                        searchEnabled: true,
                        removeItemButton: false,
                        placeholder: true,
                        placeholderValue: "Seleccione un área",
                        shouldSort: false
                    });

                    areaChoices.setChoices(options, 'value', 'label', true);
                },
                error: function (xhr) {
                    console.error('Error al cargar áreas:', xhr.responseText);
                }
            });
        }

        // Cargar áreas al abrir el modal
        $('#addEmployeeModal').on('shown.bs.modal', function () {
            cargarAreasParaAgregar();
        });
    });
</script>
<script>
    
    $(document).ready(function() {
        var table = $('#empleados-table').DataTable({
            processing: true,
            serverSide: true,
            
            ajax: {
                url: '{{ url("empleados/data") }}',
                data: function(d) {
                    d.estatus = $('#estatusFilter').val(); // envía el filtro
                }
            },
            columns: [
                { data: 'No_Empleado', name: 'No_Empleado' },
                { data: 'Nip', name: 'Nip' },
                { data: 'No_Tarjeta', name: 'No_Tarjeta' },
                { data: 'Nombre', name: 'Nombre' },
                { data: 'APaterno', name: 'APaterno' },
                { data: 'AMaterno', name: 'AMaterno', defaultContent: '' },
                { data: 'NArea', name: 'NArea' },
                {
                    data: null,
                    name: 'Permisos',
                    render: function(data, type, row) {
                        return `<a href="/cli/areas/permissions/${row.Id_Area}" class="btn btn-xs btn-info">Permisos ... <i class="fas fa-user-tag"></i></a>`;
                    }
                },
                {
                    data: null,
                    name: 'Editar',
                    render: function(data, type, row) {
                        return `<button class="btn btn-xs btn-warning edit-btn" data-id="${row.Id_Empleado}" data-nip="${row.Nip}" data-notarjeta="${row.No_Tarjeta}" data-nombre="${row.Nombre}" data-apaterno="${row.APaterno}" data-amaterno="${row.AMaterno}" data-area="${row.Id_Area}">&nbsp;&nbsp; Editar &nbsp;&nbsp; <i class="fas fa-user-edit"></i></button>`;
                    }
                },
                {
                    data: null,
                    name: 'Eliminar',
                    render: function(data, type, row) {
                        return `<button class="btn btn-xs btn-danger" onclick="confirmDelete(${row.Id_Empleado}, '${row.Nombre} ${row.APaterno} ${row.AMaterno}')">Eliminar <i class="fas fa-trash"></i></button>`;
                    }
                },
                {
                    data: 'Txt_Estatus', 
                    name: 'Txt_Estatus',
                    render: function(data, type, row) {
                        var btnClass = row.Txt_Estatus === 'Alta' ? 'btn-danger' : 'btn-success';
                        var btnText = row.Txt_Estatus === 'Alta' ? 'Desactivar <i class="fas fa-lock"></i>' : '&nbsp;&nbsp;Activar&nbsp;&nbsp; <i class="fas fa-lock-open"></i>';
                        return `<button class="btn btn-xs ${btnClass}" onclick="toggleStatus(${row.Id_Empleado})">${btnText}</button>`;
                    }
                },
                { data: 'MFecha', name: 'MFecha' }
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

        window.toggleStatus = function(employeeId) {
            $.ajax({
                url: `/empleado/toggle-status/${employeeId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload(null, false);
                },
                error: function() {
                    alert('Error al cambiar el estado del empleado.');
                }
            });
        };

        window.confirmDelete = function(employeeId, employeeName) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Se eliminará al empleado : [${employeeName}] `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/empleado/delete/${employeeId}`,
                        method: 'GET',
                        success: function(response) {
                            table.ajax.reload(null, false);
                            Swal.fire('Eliminado!', `El empleado ${employeeName} ha sido eliminado.`, 'success');
                        },
                        error: function() {
                            Swal.fire('Error!', 'Hubo un problema al eliminar el empleado.', 'error');
                        }
                    });
                }
            });
        };

        

        $('#addEmployeeForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '/empleado/add',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addEmployeeModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Empleado agregado correctamente.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    for (var key in errors) {
                        errorMessage += errors[key] + '\n';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                }
            });
        });

        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#editId').val();
            var nip = $('#nip').val();
            var notarjeta = $('#notarjeta').val();
            var nombre = $('#nombre').val();
            var apaterno = $('#apaterno').val();
            var amaterno = $('#amaterno').val();
            var area = $('#area').val();
            $.ajax({
                url: `/empleado/update/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id:id,
                    nip: nip,
                    notarjeta: notarjeta,
                    nombre: nombre,
                    apaterno: apaterno,
                    amaterno: amaterno,
                    area: area
                },
                success: function(result) {
                    table.ajax.reload();
                    $('#editModal').modal('hide');
                    Swal.fire('Éxito', 'Empleado actualizado con éxito.', 'success');
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON);
                    Swal.fire('Error', 'Hubo un error al actualizar el empleado.', 'error');
                }
            });
        });

        $('#empleados-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var nip = $(this).data('nip');
            var notarjeta = $(this).data('notarjeta');
            var nombre = $(this).data('nombre');
            var apaterno = $(this).data('apaterno');
            var amaterno = $(this).data('amaterno');
            var area = $(this).data('area');
            openEditModal(id, nip, notarjeta, nombre, apaterno, amaterno, area);
        });

        function actualizarLuzEstatus() {
            const valor = $('#estatusFilter').val();
            const luz = $('#estatusIndicator');

            luz.removeClass('bg-success bg-danger bg-secondary');

            if (valor === 'Alta') {
                luz.addClass('bg-success'); // Verde
            } else if (valor === 'Baja') {
                luz.addClass('bg-danger'); // Rojo
            } else {
                luz.addClass('bg-secondary'); // Gris para "Todos"
            }
        }

        // Ejecutar al cargar la página
        actualizarLuzEstatus();

        // Actualizar cada vez que se cambia el filtro
        $('#estatusFilter').on('change', function () {
            actualizarLuzEstatus();
            table.ajax.reload();
        });
    });

    function openEditModal(id, nip, notarjeta, nombre, apaterno, amaterno, idArea) {
        $('#editId').val(id);
        $('#nip').val(nip);
        $('#notarjeta').val(notarjeta);
        $('#nombre').val(nombre);
        $('#apaterno').val(apaterno);
        $('#amaterno').val(amaterno);
        var areaSelect = document.getElementById("area");
        if (areaSelect.choicesInstance) {
            areaSelect.choicesInstance.destroy();
        }
        areaSelect.innerHTML = '';
        $.ajax({
            url: '{{route('areas.data', [], false)}}',
            method: 'GET',
            success: function(data) {
                var idAreaStr = idArea.toString();
                var options = [];
                var currentArea = data.find(area => area.Id_Area === idAreaStr);
                if (currentArea) {
                    options.push({ value: currentArea.Id_Area, label: currentArea.Txt_Nombre, selected: true });
                    data = data.filter(area => area.Id_Area !== idAreaStr);
                }
                data.forEach(area => {
                    options.push({ value: area.Id_Area, label: area.Txt_Nombre });
                });
                var choices = new Choices(areaSelect, {
                    searchEnabled: true,
                    removeItemButton: false,
                    placeholder: true,
                    placeholderValue: "Seleccione un área",
                    shouldSort: false,
                });
                choices.setChoices(options, 'value', 'label', true);
                areaSelect.choicesInstance = choices;
            },
            error: function(xhr) {
                console.error('Error al cargar las áreas:', xhr.responseText);
            }
        });
        $('#editModal').modal('show');

    }

    
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('status'))
            let status = "{{ session('status') }}";
            let message = "{{ session('message') }}";
            Swal.fire({
                icon: status,
                title: status === 'success' ? 'Éxito' : 'Error',
                html: message,
                confirmButtonText: 'Aceptar'
            });
        @endif
    });
</script>
@stop
