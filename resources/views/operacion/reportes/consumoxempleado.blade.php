@extends('adminlte::page')

@section('title', __('Reporte Consumo por Empleado'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                        class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Reporte de Consumo por Empleado') }}
            </h4>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="card w-100">
            <div class="card-header">
                <h5 class="card-title">Selección de Planta</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <label for="selectPlanta"><strong><i class="fas fa-industry"></i> Planta a
                                Consultar:</strong></label>
                        <select id="selectPlanta" class="form-control select2">
                            <option value="">Seleccione una planta...</option>
                            @foreach($plantas as $planta)
                                <option value="{{ $planta->Id_Planta }}">{{ $planta->Txt_Nombre_Planta }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card w-100 mt-3" id="reportCard" style="display: none;">
            <div class="card-header">
                <h5 class="card-title">Tabla de Consumos (Datos Censurados)</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                <div class="row mb-3 align-items-end">
                    <div class="col-md-2">
                        <label for="startDate">Fecha Inicio:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" id="startDate" class="form-control" placeholder="Fecha Inicio">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="endDate">Fecha Fin:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" id="endDate" class="form-control" placeholder="Fecha Fin">
                        </div>
                    </div>
                    <div class="col-md-4">
                       <label for="selectVending">Vending:</label>
                       <select id="selectVending" class="form-control" multiple>
                           <!-- Options populated dynamically -->
                       </select>
                   </div>
                    <div class="col-md-2">
                        <button id="btnFilter" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button id="btnExport" class="btn btn-outline-success btn-block">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <table id="consumptionReport" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre (Censurado)</th>
                                    <th>No. Empleado</th>
                                    <th>Área</th>
                                    <th>Producto</th>
                                    <th>Código Urvina</th>
                                    <th>Selección</th>
                                    <th>Vending</th>
                                    <th>Fecha</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@stop

@section('js')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    function goBack() {
        window.history.back();
    }

    $(document).ready(function () {
        console.log("Document ready - initializing components");
        
        // Initialize Select2 ONLY for Plant selector (as requested to keep Vending with Choices)
        $('#selectPlanta').select2({
            placeholder: "Seleccione una opción",
            allowClear: true,
            width: '100%',
            theme: 'bootstrap4'
        });

        // Initialize Choices.js for Vending selector
        let vendingChoices = new Choices('#selectVending', {
            removeItemButton: true,
            searchEnabled: true,
            searchPlaceholderValue: 'Buscar Vending...',
            placeholder: true,
            placeholderValue: 'Todas las Vendings',
            itemSelectText: '',
            shouldSort: false
        });

        $("#startDate, #endDate").datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true });

        let table;

        $('#selectPlanta').on('change', function () {
            var idPlanta = $(this).val();
            console.log("Plant selection changed: " + idPlanta);
            
            // Clear and reset Vending select
            $('#selectVending').empty();

            if (idPlanta) {
                // Fetch Vendings for the selected Plant
                $.ajax({
                    url: '{{ route("op.get_vendings_by_plant") }}',
                    type: 'POST',
                    data: {
                        idPlanta: idPlanta,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Clear and Destroy existing Choices instance
                        // vendingChoices.clearStore(); // clearStore removes active items but keeps choices? No, destroy is safer for full reload.
                        // Actually clearStore removes choices too? 
                        // Let's stick to destroy and re-init pattern from operadores.blade.php
                        
                        vendingChoices.destroy();
                        
                        var $select = $('#selectVending');
                        $select.empty(); // Clear DOM options
                        
                        if(response && response.length > 0){
                            $.each(response, function(index, vending) {
                                $select.append('<option value="' + vending.Id_Maquina + '">' + vending.Txt_Nombre + '</option>');
                            });
                        }
                        
                        // Re-initialize Choices
                        vendingChoices = new Choices('#selectVending', {
                            removeItemButton: true,
                            searchEnabled: true,
                            searchPlaceholderValue: 'Buscar Vending...',
                            placeholder: true,
                            placeholderValue: 'Todas las Vendings',
                            itemSelectText: '',
                            shouldSort: false
                        });
                    },
                    error: function(xhr) {
                        console.error("Error fetching vendings:", xhr);
                    }
                });

                $('#reportCard').show();
                // We don't load table immediately on plant change anymore, user should filter or we load with defaults?
                // Original behavior was loadTable(idPlanta). Maintaining it but passing null for vending initially.
                loadTable(idPlanta, []);
            } else {
                $('#reportCard').hide();
                if (table) table.clear().draw();
            }
        });

        // Trigger filter on Vending change as well? 
        // Or kept to "Filter" button? Original code had "Filter" button for dates but loaded table immediately on plant selection.
        // Let's reload table on vending selection too for better UX, or just wait for filter?
        // Given existing code loaded table on plant selection, let's keep it responsive.

        // Actually, the previous code had a "Filtrar" button for dates. 
        // Let's make "Filtrar" button handle everything to avoid too many requests?
        // Or update table on Vending change? The user requested "Inserta tambien un filto select", usually implies usage like the others.
        // For consistency with typical dashboards: Vending change -> Update Table.
        
        $('#btnFilter').on('click', function () {
            console.log("Filter button clicked");
            // Reload table with current selections
            var idPlanta = $('#selectPlanta').val();
            // Choices.js updates the underlying select value, but let's be sure
            var vendingId = $('#selectVending').val(); 
            if(idPlanta) {
                loadTable(idPlanta, vendingId);
            }
        });

        $('#btnExport').on('click', function () {
            var idPlanta = $('#selectPlanta').val();
            var vendingId = $('#selectVending').val();
            var startDate = $('#startDate').val();
       var endDate = $('#endDate').val();
            
            if (!idPlanta) {
                alert("Seleccione una planta primero.");
                return;
            }

            // Serialize array for URL if needed, but standard query param array syntax is handled by backend usually headers?
            // For Excel export via GET, we need to handle array parameters correctly.
            // Construct URL parameters manually for array.
            
            var url = '{{ route("op.consumoxempleado.export") }}' + '?idPlanta=' + idPlanta + '&startDate=' + startDate + '&endDate=' + endDate;
            
            if (vendingId && vendingId.length > 0) {
                // If it's an array, append each
                if(Array.isArray(vendingId)){
                    vendingId.forEach(function(id) {
                        url += '&vendingId[]=' + id;
                    });
                } else {
                     url += '&vendingId[]=' + vendingId;
                }
            }
            
            window.location.href = url;
        });

        function loadTable(idPlanta, vendingId) {
            console.log("Loading table for plant: " + idPlanta + ", vending: " + vendingId);
            if ($.fn.DataTable.isDataTable('#consumptionReport')) {
                table.clear().destroy();
            }

            table = $('#consumptionReport').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("op.consumoxempleado.data") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.idPlanta = idPlanta;
                        d.vendingId = vendingId;
                        d.startDate = $('#startDate').val();
                        d.endDate = $('#endDate').val();
                        console.log("Sending AJAX request with data:", d);
                    },
                    error: function (xhr, error, code) {
                        console.error("AJAX Error:", xhr, error, code);
                    }
                },
                lengthMenu: [[100, 500, 1000, -1], [100, 500, 1000, "Todos"]],
                pageLength: 100,
                columns: [
                    { data: 'Nombre' },
                    { data: 'Numero_de_empleado' },
                    { data: 'Area' },
                    { data: 'Producto' },
                    { data: 'Codigo_Urvina' },
                    { data: 'Seleccion' },
                    { data: 'Vending' },
                    { data: 'Fecha' },
                    { data: 'Cantidad' }
                ],
                responsive: true,
                scrollX: true,
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    paginate: { first: "Primero", previous: "Anterior", next: "Siguiente", last: "Último" },
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay datos disponibles"
                }
            });
        }
    });
</script>
@stop