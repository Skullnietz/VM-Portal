@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Empleados'))

@section('content_header')
<div class="container">
        <div class="row">
            <!-- Columna de la izquierda con alineación a la izquierda -->
            <div class="col-2 d-flex align-items-center">
                <h4 class="mb-0">
                    <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Empleados') }}
                </h4>
            </div>

            <!-- Columna de la derecha con alineación a la derecha -->
            <div class="col-10 d-flex justify-content-end align-items-center">
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
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Administrador de Empleados</h5>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-key"></i>&nbsp;&nbsp;| NIP</span>
                                </div>
                                <input type="number" class="form-control" id="nip" name="nip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-address-card"></i>&nbsp;&nbsp;| ID Tarjeta</span>
                                </div>
                                <input type="number" class="form-control" id="notarjeta" name="notarjeta" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Nombre</span>
                                </div>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Paterno</span>
                                </div>
                                <input type="text" class="form-control" id="apaterno" name="apaterno" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Materno</span>
                                </div>
                                <input type="text" class="form-control" id="amaterno" name="amaterno">
                            </div>
                        </div>
                        <div class="form-group"> 
                            <label for="area"><i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Área &nbsp;| &nbsp;&nbsp;Permisos de Producto</label>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadCsvModalLabel">Subir archivo CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Agregar Empleado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm">
                <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-id-card-alt"></i>&nbsp;&nbsp;| N° Empleado</span>
                            </div>
                            <input type="number" class="form-control" id="no_empleado" name="no_empleado" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-key"></i>&nbsp;&nbsp;| NIP</span>
                            </div>
                            <input type="number" class="form-control" id="nip" name="nip" >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-address-card"></i>&nbsp;&nbsp;| ID Tarjeta</span>
                            </div>
                            <input type="number" class="form-control" id="no_tarjeta" name="no_tarjeta">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Nombre</span>
                            </div>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Paterno</span>
                            </div>
                            <input type="text" class="form-control" id="apaterno" name="apaterno" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Materno</span>
                            </div>
                            <input type="text" class="form-control" id="amaterno" name="amaterno">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="area"><i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Área &nbsp;| &nbsp;&nbsp;Permisos de Producto</label>
                        <select class="form-control" id="area" name="area" required>
                            @foreach ($areas as $area)
                                <option value="{{ $area->Id_Area }}">{{ $area->Txt_Nombre }}</option>
                            @endforeach
                        </select>
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
<style>
    .input-group-text-fixed {
        min-width: 160px;
        text-align: center;
    }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var table = $('#empleados-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('empleados.data') !!}',
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


        window.toggleStatus = function(employeeId) {
            $.ajax({
                url: `/empleado/toggle-status/${employeeId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload(null, false); // false to keep current paging
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
                            table.ajax.reload(null, false); // false to keep current paging
                            Swal.fire(
                                'Eliminado!',
                                `El empleado ${employeeName} ha sido eliminado.`,
                                'success'
                            );
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al eliminar el empleado.',
                                'error'
                            );
                        }
                    });
                }
            });
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#addEmployeeForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '/empleado/add', // Ajusta la URL a la ruta de tu controlador de Laravel
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

        // Manejo del formulario de edición
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
                type: 'PUT',
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
                    table.ajax.reload(); // Recarga la tabla después de actualizar el empleado
                    $('#editModal').modal('hide');
                    Swal.fire('Éxito', 'Empleado actualizado con éxito.', 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Hubo un error al actualizar el empleado: ' + xhr.responseJSON.message, 'error');
                    console.log(xhr.responseJSON.errors); 
                }
            });
        });

        // Función para abrir el modal de edición
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
    });

    function openEditModal(id, nip, notarjeta, nombre, apaterno, amaterno, idArea) {
        // Llenar el formulario con los datos del empleado
        $('#editId').val(id);
        $('#nip').val(nip);
        $('#notarjeta').val(notarjeta);
        $('#nombre').val(nombre);
        $('#apaterno').val(apaterno);
        $('#amaterno').val(amaterno);

        // Cargar opciones de áreas en el select
        $.ajax({
        url: '{!! route('areas.data') !!}', // Ruta para obtener áreas
        method: 'GET',
        success: function(data) {
            console.log('Datos de áreas recibidos:', data); // Verificar datos recibidos
            var $areaSelect = $('#area');
            $areaSelect.empty(); // Limpiar opciones existentes

            // Convertir idArea a cadena para comparación
            var idAreaStr = idArea.toString();

            // Verificar que el área actual del empleado exista en los datos recibidos
            var currentArea = data.find(area => area.Id_Area === idAreaStr);
            console.log('Área actual del empleado:', currentArea); // Verificar área actual
            if (currentArea) {
                // Agregar el área actual del empleado como la primera opción
                $areaSelect.append(`<option value="${currentArea.Id_Area}" selected>${currentArea.Txt_Nombre}</option>`);
                // Eliminar el área actual de las opciones restantes
                data = data.filter(area => area.Id_Area !== idAreaStr);
            }

            // Agregar las demás áreas
            data.forEach(area => {
                $areaSelect.append(`<option value="${area.Id_Area}">${area.Txt_Nombre}</option>`);
            });
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
