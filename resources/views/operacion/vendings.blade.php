@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Plantas de VMs'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class=" col-md-9 col-9">
            <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                        class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Plantas de VMs') }}</h4>
        </div>
        <div class="col-md-3 col-3 ml-auto">
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
                    <h5 class="card-title">
                        Vendings
                    </h5>
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
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: #fff;
        border-radius: 8px;
        font-size: 1.1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-h:hover {
        background: linear-gradient(45deg, #0056b3, #004494);
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }

    .card-bh {
        padding: 20px;
        background-color: #ffffff;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .grouped-table {
        margin-left: auto;
    }

    .table-vending thead th {
        background-color: #343a40;
        color: white;
        border: none;
    }

    .table-vending tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        transition: transform 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .btn-action {
        margin: 0 2px;
        border-radius: 20px;
        padding: 5px 15px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
            url: '/op/vendings/data', // Ruta del controlador para obtener los datos
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
                    <div class="card mb-4 shadow-sm" style="border-radius: 8px; border: none;">
                        <div class="card-header d-flex align-items-center text-white"
                             id="heading${index}" data-toggle="collapse" data-target="#collapse${index}" 
                             aria-expanded="false" aria-controls="collapse${index}" 
                             style="cursor: pointer; background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%); border-radius: 8px;">
                            <img src="${plantaImage}" alt="Planta" class="rounded-circle mr-3 border border-white" style="width: 45px; height: 45px; padding: 2px;">
                            <h5 class="mb-0 font-weight-bold">
                                ${planta}
                            </h5>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>
                        <div id="collapse${index}" class="collapse" aria-labelledby="heading${index}" data-parent="#groupedVendingsContainer">
                            <div class="card-body bg-light">
                                <table id="table${index}" class="display table table-hover table-vending" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Serie</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
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
                            <td class="align-middle font-weight-bold text-primary">${registro.Txt_Nombre}</td>
                            <td class="align-middle">${registro.Txt_Serie_Maquina}</td>
                            <td class="align-middle"><span class="badge badge-info">${registro.Txt_Tipo_Maquina}</span></td>
                            <td class="align-middle">
                                <a href="stock/rellenar/${registro.Id_Maquina}" class="btn btn-success btn-sm btn-action">
                                    <i class="fas fa-box-open mr-1"></i> Rellenar
                                </a>
                                <button class="btn btn-warning btn-sm btn-action check-missing-btn" data-id="${registro.Id_Maquina}">
                                    <i class="fas fa-clipboard-list mr-1"></i> Faltantes
                                </button>
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

                    // Ajustar columnas al mostrar el acordeón
                    $(`#collapse${index}`).on('shown.bs.collapse', function () {
                        $(this).find('table').DataTable().columns.adjust().draw();
                    });

                    // Evento para alternar el icono del header al colapsar
                    $('.card-header').on('click', function () {
                        const icon = $(this).find('i.fas.fa-chevron-down, i.fas.fa-chevron-up');
                        icon.toggleClass('fa-chevron-down fa-chevron-up');
                    });
                });

                // Event listener for "Faltantes" button
                $(document).on('click', '.check-missing-btn', function () {
                    const idMaquina = $(this).data('id');
                    window.location.href = `/op/vending/download-missing-items/${idMaquina}`;
                });
            }
        });
    });
</script>
<script>
    function goBack() {
        window.history.back();
    }
</script>
@stop