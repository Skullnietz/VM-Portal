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
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        font-size: 1rem;
    }

    .card-h:hover {
        background-color: #0056b3;
    }

    .card-bh {
        padding: 15px;
        background-color: #f9f9f9;
    }

    .grouped-table {
        margin-left: auto; /* Alinea el icono completamente a la derecha */
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
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center bg-light text-white"
                             id="heading${index}" data-toggle="collapse" data-target="#collapse${index}" 
                             aria-expanded="false" aria-controls="collapse${index}" style="cursor: pointer;">
                            <img src="${plantaImage}" alt="Planta" class="rounded-circle mr-3" style="width: 40px; height: 40px;">
                            <h5 class="mb-0">
                                ${planta}
                            </h5>
                            <i class="fas fa-chevron-down ml-auto"></i>
                        </div>
                        <div id="collapse${index}" class="collapse" aria-labelledby="heading${index}" data-parent="#groupedVendingsContainer">
                            <div class="card-body">
                                <table id="table${index}" class=" display table table-striped table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Serie</th>
                                            <th>Tipo</th>
                                            <th>Stock</th>
                                            
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
                            <td>${registro.Txt_Nombre}</td>
                            <td>${registro.Txt_Serie_Maquina}</td>
                            <td>${registro.Txt_Tipo_Maquina}</td>
                            <td>
                            <a href="stock/rellenar/${registro.Id_Maquina}" class="btn btn-info btn-sm edit-btn">
                                    Rellenar
                            </a>
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


</script>
<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop
