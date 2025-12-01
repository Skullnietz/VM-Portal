@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Administración de VMs'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Administración de VMs') }}</h4>
            </div>
            <div class="col-md-3 col-3 ml-auto">
            </div>


        </div>
    </div>
@stop

@section('content')
<!-- Modal para editar la máquina -->
<div class="modal fade" id="editVendingModal" tabindex="-1" role="dialog" aria-labelledby="editVendingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVendingModalLabel">Editar Máquina</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editVendingForm">
                    <input type="hidden" id="editMachineId" name="id_maquina" />

                    <div class="form-group">
                        <label for="editNombre">Nombre</label>
                        <input type="text" class="form-control" id="editNombre" name="Txt_Nombre" required />
                    </div>

                    <div class="form-group">
                        <label for="editSerie">Serie</label>
                        <input type="text" class="form-control" id="editSerie" name="Txt_Serie_Maquina" required />
                    </div>

                    <div class="form-group">
                        <label for="editTipo">Tipo</label>
                        <input type="text" class="form-control" id="editTipo" name="Txt_Tipo_Maquina" required />
                    </div>

                    <div class="form-group">
                        <label for="editEstatus">Estatus</label>
                        <select class="form-control" id="editEstatus" name="Txt_Estatus" required>
                            <option value="Alta">Alta</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editCapacidad">Capacidad</label>
                        <input type="number" class="form-control" id="editCapacidad" name="Capacidad" required />
                    </div>

                    <!-- Nuevo campo para seleccionar el dispositivo -->
                    <div class="form-group">
                        <label for="editDispositivo">Dispositivo</label>
                        <select class="form-control" id="editDispositivo" name="Id_Dispositivo" required>
                            <!-- Opciones se llenarán con JS -->
                        </select>
                    </div>

                    <button id="saveChangesButton"  class="btn btn-primary">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar una máquina expendedora -->
<div class="modal fade" id="addVendingModal" tabindex="-1" role="dialog" aria-labelledby="addVendingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVendingModalLabel">Agregar Máquina Expendedora</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addVendingForm">
                    <div class="form-group">
                        <label for="Txt_Nombre">Nombre</label>
                        <input type="text" class="form-control" id="Txt_Nombre" name="Txt_Nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Serie_Maquina">Serie de la Máquina</label>
                        <input type="text" class="form-control" id="Txt_Serie_Maquina" name="Txt_Serie_Maquina" required>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Tipo_Maquina">Tipo de Máquina</label>
                        <input type="text" class="form-control" id="Txt_Tipo_Maquina" name="Txt_Tipo_Maquina" required>
                    </div>
                    <div class="form-group">
                        <label for="Capacidad">Capacidad</label>
                        <input type="number" class="form-control" id="Capacidad" name="Capacidad" required>
                    </div>
                    <div class="form-group">
                        <label for="Id_Planta">Planta</label>
                        <select id="plantas" class="form-control" id="Id_Planta" name="Id_Planta" required>
                            <option value="">Seleccione una opcion</option>
                            @foreach ($plantas as $planta)
                            <option value="{{ $planta->Id_Planta }}">{{ $planta->Txt_Nombre_Planta }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="Id_Dispositivo">ID del Dispositivo</label>
                        <select id="disponibles" class="form-control" id="Id_Dispositivo" name="Id_Dispositivo" required>
                            <option value="">Seleccione una opcion</option>
                            @foreach ($dispositivosNoAsignados as $dispositivo)
                            <option value="{{ $dispositivo->Id_Dispositivo }}">{{ $dispositivo->Txt_Serie_Dispositivo }}</option>
                            @endforeach
                            
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title">
                            Vendings
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm bg-primary" data-toggle="modal" data-target="#addVendingModal">
                                Agregar VM <i class="fas fa-wrench"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body">
                    <div id="groupedVendingsContainer" class="accordion"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
    .card-h {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: #fff;
        border-radius: 8px;
        font-size: 1.1rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .card-h:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0,0,0,0.15);
    }

    .card-bh {
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }

    .grouped-table {
        margin-left: auto;
    }

    /* Custom Table Styling */
    .table-custom thead th {
        background-color: #343a40;
        color: white;
        border: none;
    }
    
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
        transition: all 0.2s;
    }

    .accordion .card {
        border: none;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .accordion .card-header {
        border-radius: 8px !important;
        border: none;
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    
    $(document).ready(function () {
        $('#plantas').select2({
        placeholder: "Selecciona una planta",
        allowClear: true
    });
    $('#disponibles').select2({
        placeholder: "Selecciona un dispositivo",
        allowClear: true
    });
        $('#editDispositivo').select2({
        placeholder: "Selecciona un dispositivo",
        allowClear: true
    });
    // Datos de ejemplo; en una aplicación real estos datos provendrían del backend
    $.ajax({
        url: '/vendings/data', // Ruta del controlador para obtener los datos
        method: 'GET',
        success: function (data) {
            let container = $('#groupedVendingsContainer'); // El contenedor principal para los grupos de plantas
            let index = 0;

            $.each(data, function (planta, registros) {
                index++; // Incrementamos el índice para generar identificadores únicos

                // Tomar la imagen de la planta del primer registro
                const plantaImage = registros[0].Ruta_Imagen
                    ? registros[0].Ruta_Imagen
                    : '/Images/Plantas/default.png'; // Ruta al ícono por defecto

                // Creamos un grupo colapsable para cada planta
                const plantaGroup = $(`
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center card-h"
                             id="heading${index}" data-toggle="collapse" data-target="#collapse${index}" 
                             aria-expanded="false" aria-controls="collapse${index}" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <img src="${plantaImage}" alt="Planta" class="rounded-circle mr-3 border border-white" style="width: 45px; height: 45px; object-fit: cover;">
                                <div>
                                    <h5 class="mb-0 font-weight-bold">
                                        ${planta}
                                    </h5>
                                    <small class="text-white-50"><i class="fas fa-industry mr-1"></i> Planta</small>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>
                        <div id="collapse${index}" class="collapse" aria-labelledby="heading${index}" data-parent="#groupedVendingsContainer">
                            <div class="card-body card-bh">
                                <table id="table${index}" class="display table table-hover table-custom table-bordered" style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><i class="fas fa-cube mr-1"></i> Nombre</th>
                                            <th><i class="fas fa-barcode mr-1"></i> Serie</th>
                                            <th><i class="fas fa-cogs mr-1"></i> Tipo</th>
                                            <th><i class="fas fa-toggle-on mr-1"></i> Estatus</th>
                                            <th><i class="fas fa-box mr-1"></i> Capacidad</th>
                                            <th><i class="fas fa-tools mr-1"></i> Configuración</th>
                                            <th><i class="fas fa-dolly mr-1"></i> Stock</th>
                                            <th><i class="far fa-calendar-alt mr-1"></i> Fecha Alta</th>
                                            <th><i class="fas fa-sliders-h mr-1"></i> Opciones</th> 
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `);

                container.append(plantaGroup);

                // Llenamos la tabla con los registros de esta planta
                registros.forEach((registro) => {
                    $(`#table${index} tbody`).append(`
                        <tr data-id="${registro.Id_Maquina}">
                            <td class="align-middle font-weight-bold">${registro.Txt_Nombre}</td>
                            <td class="align-middle">${registro.Txt_Serie_Maquina}</td>
                            <td class="align-middle"><span class="badge badge-info">${registro.Txt_Tipo_Maquina}</span></td>
                            <td class="align-middle">
                                <button class="btn btn-sm ${registro.Txt_Estatus === 'Alta' ? 'btn-outline-success' : 'btn-outline-danger'} font-weight-bold" 
                                        onclick="changeStatus(${registro.Id_Maquina}, '${registro.Txt_Estatus}')" style="width: 80px;">
                                    ${registro.Txt_Estatus === 'Alta' ? '<i class="fas fa-check mr-1"></i> Alta' : '<i class="fas fa-times mr-1"></i> Baja'}
                                </button>
                            </td>
                            <td class="align-middle text-center">${registro.Capacidad}</td>
                            <td class="align-middle text-center">
                            <a href="config/plano/${registro.Id_Maquina}" class="btn btn-dark btn-sm shadow-sm">
                                    <i class="fas fa-th mr-1"></i> Planograma
                            </a>
                            </td>
                            <td class="align-middle text-center">
                            <a href="Astock/rellenar/${registro.Id_Maquina}" class="btn btn-info btn-sm shadow-sm">
                                    <i class="fas fa-fill-drip mr-1"></i> Rellenar
                            </a>
                            </td>
                            <td class="align-middle">${registro.Fecha_Alta}</td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm shadow-sm" onclick="editVending(${registro.Id_Maquina})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm shadow-sm" onclick="deleteVending(${registro.Id_Maquina})" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-success btn-sm shadow-sm" onclick="window.location.href='/admin/vending/download-missing-items/${registro.Id_Maquina}'" title="Descargar Faltantes">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                </div>
                            </td>
                            
                        </tr>
                    `);
                });

                // Inicializamos DataTables
                $(`#table${index}`).DataTable({
                    searching: true,
                    columnDefs: [
                        { targets: [0, 2], searchable: true }, // Buscar por Id de máquina y Nombre
                        { targets: '_all', searchable: false } // Deshabilitar búsqueda en otras columnas
                    ],
                    responsive: true,
                    scrollX: true,
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros por página",
                        zeroRecords: "No se encontraron coincidencias",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        infoEmpty: "No hay registros disponibles",
                        infoFiltered: "(filtrado de _MAX_ registros totales)",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        },
                    }
                });

                // Evento para alternar el icono del header al colapsar
                $('.card-header').on('click', function () {
                    const target = $(this).data('target');
                    $(target).collapse('toggle');

                    const icon = $(this).find('i.fas.fa-chevron-down, i.fas.fa-chevron-up');
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                });
            });
        }
    });
});

// Función para cambiar el estatus
function changeStatus(id, currentStatus) {
    const newStatus = currentStatus === 'Alta' ? 'Baja' : 'Alta';

    $.ajax({
        url: '/vending/changeStatus', // Ruta para cambiar el estatus
        method: 'POST',
        data: {
            id_maquina: id,
            status: newStatus,
            _token: $('meta[name="csrf-token"]').attr('content') // Asegúrate de tener el token CSRF en tu página
        },
        success: function (response) {
            // Actualizar el botón con el nuevo estatus
            const btn = $(`tr[data-id="${id}"]`).find('.btn-status');
            
            // Cambiar el color y el texto del botón
            btn.removeClass(currentStatus === 'Alta' ? 'btn-success' : 'btn-danger')
               .addClass(newStatus === 'Alta' ? 'btn-success' : 'btn-danger')
               .text(newStatus);
            
            // Actualizar el evento onclick para el nuevo estatus
            btn.attr('onclick', `changeStatus(${id}, '${newStatus}')`);
        }
    });
}

// Función para abrir el modal con los datos de la máquina
// Función para abrir el modal con los datos de la máquina
function editVending(id) {
    // Realizamos una llamada AJAX para obtener los detalles de la máquina
    $.ajax({
        url: '/vending/edit/' + id, // Ruta para obtener los datos de la máquina
        method: 'GET',
        success: function (data) {
            // Llenamos los campos del formulario con los datos obtenidos
            $('#editMachineId').val(data.Id_Maquina);
            $('#editNombre').val(data.Txt_Nombre);
            $('#editSerie').val(data.Txt_Serie_Maquina);
            $('#editTipo').val(data.Txt_Tipo_Maquina);
            $('#editEstatus').val(data.Txt_Estatus);
            $('#editCapacidad').val(data.Capacidad);
            

            // Llamamos para llenar el select2 con los dispositivos disponibles
            loadAvailableDevices(data.Id_Dispositivo); // Le pasamos el dispositivo actual

            // Mostramos el modal
            $('#editVendingModal').modal('show');
        }
    });
}


// Función para cargar dispositivos disponibles
function loadAvailableDevices(currentDeviceId) {
    $.ajax({
        url: '/vending/devices/' + currentDeviceId, // Enviamos el ID del dispositivo actual
        method: 'GET',
        success: function (data) {
            $('#editDispositivo').empty();

            // Añadimos las opciones
            data.devices.forEach(function (device) {
                let option = new Option(device.Txt_Serie_Dispositivo, device.Id_Dispositivo, false, false);
                $('#editDispositivo').append(option);
            });

            // Inicializamos y seleccionamos el dispositivo actual
            $('#editDispositivo').select2({
                placeholder: "Selecciona un dispositivo",
                allowClear: true
            });

            if (currentDeviceId) {
                $('#editDispositivo').val(currentDeviceId).trigger('change');
            }
        }
    });
}

$('#saveChangesButton').click(function (event) {
    event.preventDefault(); // Evita que el formulario se envíe

    // Obtener valores del formulario
    const id = $('#editMachineId').val();
    const nombre = $('#editNombre').val()?.trim();
    const serie = $('#editSerie').val()?.trim();
    const tipo = $('#editTipo').val()?.trim();
    const estatus = $('#editEstatus').val();
    const capacidad = $('#editCapacidad').val();
    const dispositivo = $('#editDispositivo').val();

    if (!nombre || !serie || !tipo || !estatus || !capacidad || !dispositivo) {
        alert('Por favor, complete todos los campos del formulario.');
        return;
    }

    // Enviar los datos al backend
    $.ajax({
        url: '/vending/update', // Ruta del backend
        method: 'POST',
        data: {
            id_maquina: id,
            Txt_Nombre: nombre,
            Txt_Serie_Maquina: serie,
            Txt_Tipo_Maquina: tipo,
            Txt_Estatus: estatus,
            Capacidad: capacidad,
            Id_Dispositivo: dispositivo,
            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
        },
        success: function (response) {
            if (response.success) {
                // Mostrar mensaje de éxito
                toastr.success('Los cambios se realizaron correctamente.');

                // Cerrar el modal
                $('#editVendingModal').modal('hide');

                // Esperar un momento antes de recargar la página
                setTimeout(() => {
                    location.reload();
                }, 2000); // 2 segundos de espera
            } else {
                toastr.error('Hubo un problema al guardar los cambios.');
            }
        },
        error: function (error) {
            toastr.error('Error al guardar los cambios. Intente nuevamente.');
        }
    });
});

    // Manejar el envío del formulario
    $('#addVendingForm').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '/vending/create',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de incluir esto
            },
            success: function(response) {
                if (response.success) {
                    $('#addVendingModal').modal('hide');
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Error al procesar la solicitud: ' + xhr.responseJSON.message);
            }
        });
    });
// Función para eliminar la máquina
function deleteVending(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'No podrás deshacer esta acción.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/vending/delete', // Ruta para eliminar la máquina
                    method: 'POST',
                    data: {
                        id_maquina: id,
                        _token: $('meta[name="csrf-token"]').attr('content') // Token CSRF
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            // Eliminar la fila de la tabla
                            $(`tr[data-id="${id}"]`).remove();
                            Swal.fire('Eliminada', response.message, 'success');
                        } else if (response.status === 'error') {
                            // Mostrar mensaje de error
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Ocurrió un problema al intentar eliminar la máquina.', 'error');
                    }
                });
            }
        });
    }
</script>
<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop
