@extends('adminlte::page')

@section('title', __('Reporte Inventario VM'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Inventario Vendings') }}
            </h4>
        </div>
        <div class="col text-right">
        <form id="export-form" action="{{ route('export.inventariovm') }}" method="GET">
    <button type="submit" class="btn btn-success">Exportar a Excel&nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i></button>
</form>
        </div>
    </div>
</div>
    
    
@stop

@section('content')
    <div class="container">
    <div class="row mb-2">
        
        <div class="card">
        <div  class="card-header">
                        <h5 class="card-title">Tabla de Inventario Vending Machine</h5>
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
                <div class="row">
                    <!-- Inputs para el rango de fechas -->
            
                
            <div class="row">
            <div class="col">
                <table id="consumptionReport" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                        <th>Nombre Máquina</th>
            <th>Serie Máquina</th>
            <th>Tipo Máquina</th>
            <th>Almacenamiento (%)</th>
            <th>Estatus Máquina</th>
            <th>Serie Dispositivo</th>
            <th>Estatus Dispositivo</th>
            <th>Fecha Alta</th>
            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
<br>
    

            </div>
        </div>
        <!-- Nuevo Card para las Gráficas -->
         
        <div id="stockReportContainer" style="display: none" class="card">
        <div class="card-header">
                        <h5 class="card-title">Tabla de Stock Producto</h5>
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
                <div class="row">
                    <!-- Inputs para el rango de fechas -->
            
                
            <div class="row">
            <div class="col">
                <table id="consumptionReportStock" class="table table-striped table-bordered" style="width:100%; ">
                    <thead>
                        <tr>
                        <th>Nombre Máquina</th>
            <th>Articulo</th>
            <th>Cantidad Max</th>
            <th>Existencias (Stock)</th>
            <th>Almacenamiento</th>
                        </tr>
                    </thead>
                </table>
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
/* Estilo para celdas con texto largo */
.dataTables_wrapper .dataTables_scroll .dataTables_scrollBody .dataTables_scroll .dataTables_scrollBody .dataTables_scroll .dataTables_scrollBody td {
    max-width: 200px; /* Ajusta el tamaño máximo según sea necesario */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    position: relative;
}

/* Estilo para mostrar el texto completo al pasar el ratón */
.tooltip {
    position: absolute;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s;
    z-index: 10;
    max-width: 300px; /* Ajusta el tamaño máximo según sea necesario */
    white-space: normal; /* Permite el salto de línea */
}

.dataTables_wrapper .dataTables_scroll .dataTables_scrollBody td:hover .tooltip {
    visibility: visible;
    opacity: 1;
}
.dataTables_filter {
    display: none;
}
.progress {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 4px;
        }
        .progress-bar {
            background-color: #4caf50;
            height: 20px;
            line-height: 20px;
            color: white;
            text-align: center;
            border-radius: 4px;
        }
        .progress-barS {
            background-color: #0F0;
            height: 20px;
            line-height: 20px;
            color: white;
            text-align: center;
            border-radius: 4px;
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .status-high {
            background-color: green;
        }
        .status-low {
            background-color: red;
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
   document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario y de los inputs
    const exportForm = document.getElementById('export-form');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    // Función para actualizar los campos ocultos con los arrays seleccionados
    function updateHiddenFields() {
       
        // Si tienes un rango de fechas
        if (startDateInput && endDateInput) {
            document.getElementById('filter-dateRange').value = startDateInput.value + ' - ' + endDateInput.value;
        }
    }

    // Evento de clic en el botón de exportación
    exportForm.addEventListener('submit', function(event) {
        // Actualiza los campos ocultos antes de enviar el formulario
        updateHiddenFields();
    });
});
</script>
<script>
   $(document).ready(function() {
    var inventoryTable = $('#consumptionReport').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ url("/getinventariovm/data") }}',
            error: function(xhr, error, thrown) {
                console.error("Error en la solicitud AJAX:", error, thrown);
                alert("Hubo un problema al cargar los datos. Inténtalo de nuevo más tarde.");
            }
        },
        columns: [
            { data: 'Txt_Nombre', title: 'Nombre Máquina' },
            { data: 'Txt_Serie_Maquina', title: 'Serie Máquina' },
            { data: 'Txt_Tipo_Maquina', title: 'Tipo Máquina' },
            { 
                data: 'Almacenamiento', 
                title: 'Almacenamiento (%)', 
                render: function(data) {
                    var percentage = (data !== undefined && data !== null) ? data : 0;
                    return `
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: ${percentage}%;" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">${percentage}%</div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Estatus_Maquina', 
                title: 'Estatus Máquina',
                render: function(data) {
                    var statusClass = data === 'Alta' ? 'status-high' : 'status-low';
                    return `<span class="status-indicator ${statusClass}"></span> ${data}`;
                }
            },
            { data: 'Txt_Serie_Dispositivo', title: 'Serie Dispositivo' },
            { 
                data: 'Estatus_Dispositivo', 
                title: 'Estatus Dispositivo',
                render: function(data) {
                    var statusClass = data === 'Alta' ? 'status-high' : 'status-low';
                    return `<span class="status-indicator ${statusClass}"></span> ${data}`;
                }
            },
            { data: null, title: 'Acciones', render: function(data, type, row) {
                return `<button class="btn btn-primary toggle-btn" data-id="${row.Id_Maquina}">Ver Stock</button>`;
            }},
            { data: 'Fecha_Alta', title: 'Fecha Alta' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
        }
    });

    $('#consumptionReport tbody').on('click', '.toggle-btn', function() {
        var tr = $(this).closest('tr');
        var row = inventoryTable.row(tr);

        if (row.child.isShown()) {
            // Si la fila está mostrada, ocultar los detalles
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Si la fila está oculta, cargar y mostrar los detalles
            var maquinaId = $(this).data('id');
            var url = `http://127.0.0.1:8000/getstockvm/data/${maquinaId}`;

            if (!$.fn.DataTable.isDataTable('#consumptionReportStock')) {
                $('#consumptionReportStock').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: url,
                        dataSrc: function(response) {
                            console.log("Respuesta del servidor:", response); // Verifica la respuesta
                            return response.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.error("Error en la solicitud AJAX:", error, thrown);
                            alert("Hubo un problema al cargar los datos del stock. Inténtalo de nuevo más tarde.");
                        }
                    },
                    columns: [
                        { data: 'Nombre_Vending', name: 'Nombre_Vending', title: 'Nombre Vending' },
                        { data: 'Articulo', name: 'Articulo', title: 'Artículo' },
                        { data: 'Total_Stock', name: 'Total_Stock', title: 'Stock' },
                        { 
                            data: null,
                            title: 'Almacenamiento',
                            render: function(data, type, row) {
                                var porcentaje = row.Total_Cantidad_Max > 0 ? (row.Total_Stock / row.Total_Cantidad_Max) * 100 : 0;
                                return `
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                            style="width: ${porcentaje.toFixed(2)}%;" 
                                            aria-valuenow="${porcentaje.toFixed(2)}" aria-valuemin="0" aria-valuemax="100">
                                            ${porcentaje.toFixed(2)}%
                                        </div>
                                    </div>`;
                            }
                        },
                        { data: 'Total_Cantidad_Max', name: 'Total_Cantidad_Max', title: 'Cant. Máx' }
                    ]
                });
            }

            var stockReportTable = $('#consumptionReportStock').DataTable();
            stockReportTable.ajax.url(url).load(function() {
                var stockData = stockReportTable.rows().data();
                console.log("Datos del stock:", stockData.toArray()); // Verifica los datos
                var detalleHTML = `<table class="table table-sm">
                    <thead><tr><th>Nombre Vending</th><th>Artículo</th><th>Stock</th><th>Almacenamiento</th><th>Cant. Máx</th></tr></thead>
                    <tbody>`;

                stockData.each(function(data) {
                    var porcentaje = data.Total_Cantidad_Max > 0 ? (data.Total_Stock / data.Total_Cantidad_Max) * 100 : 0;
                    detalleHTML += `
                    <tr>
                        <td>${data.Nombre_Vending}</td>
                        <td>${data.Articulo}</td>
                        <td>${data.Total_Stock}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                    style="width: ${porcentaje.toFixed(2)}%;" 
                                    aria-valuenow="${porcentaje.toFixed(2)}" aria-valuemin="0" aria-valuemax="100">
                                    ${porcentaje.toFixed(2)}%
                                </div>
                            </div>
                        </td>
                        <td>${data.Total_Cantidad_Max}</td>
                    </tr>`;
                });
                detalleHTML += '</tbody></table>';

                row.child(detalleHTML).show(); // Mostrar los detalles como una fila hija
                tr.addClass('shown');
            });
        }
    });
});
</script>

@stop
