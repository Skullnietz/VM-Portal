@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', 'Permisos de Artículos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-lock mr-2"></i>Permisos de Artículos</h1>
        <a href="{{ route('exportar.permisos') }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel mr-1"></i> Exportar Excel
        </a>
    </div>
@stop

@section('content')

{{-- Panel de edición masiva (sticky) --}}
<div id="bulk-panel" class="card card-primary card-outline mb-2" style="display:none; position:sticky; top:60px; z-index:999;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <span class="badge badge-primary mr-2" id="bulk-count" style="font-size:1em;">0 seleccionados</span>

            <div class="input-group input-group-sm" style="width:165px;">
                <div class="input-group-prepend"><span class="input-group-text">Frecuencia</span></div>
                <input type="number" id="bulk-frecuencia" class="form-control" min="0" max="360" placeholder="—">
                <div class="input-group-append"><span class="input-group-text">días</span></div>
            </div>

            <div class="input-group input-group-sm" style="width:140px;">
                <div class="input-group-prepend"><span class="input-group-text">Cantidad</span></div>
                <input type="number" id="bulk-cantidad" class="form-control" min="0" max="999" placeholder="—">
            </div>

            <select id="bulk-status" class="form-control form-control-sm" style="width:110px;">
                <option value="">— Estatus —</option>
                <option value="Alta">Alta</option>
                <option value="Baja">Baja</option>
            </select>

            <button id="bulk-apply" class="btn btn-sm btn-success">
                <i class="fas fa-check mr-1"></i> Aplicar
            </button>
            <button id="bulk-deselect" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times mr-1"></i> Deseleccionar
            </button>
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="card card-outline card-secondary mb-2">
    <div class="card-body py-2">
        <div class="row align-items-end">
            <div class="col-md-4 col-sm-6 mb-1">
                <label class="mb-0 small font-weight-bold">Área</label>
                <select id="filter-area" class="form-control form-control-sm">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->Id_Area }}">{{ $area->Txt_Nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-sm-6 mb-1">
                <label class="mb-0 small font-weight-bold">Artículo</label>
                <select id="filter-articulo" class="form-control form-control-sm" style="width:100%">
                    <option value=""></option>
                </select>
            </div>
            <div class="col-md-2 col-sm-4 mb-1">
                <label class="mb-0 small font-weight-bold">Estatus</label>
                <select id="filter-status" class="form-control form-control-sm">
                    <option value="Todos">Todos</option>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-4 mb-1 d-flex">
                <button id="btn-filtrar" class="btn btn-sm btn-primary mr-1 flex-fill">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
                <button id="btn-limpiar" class="btn btn-sm btn-outline-secondary flex-fill">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-body p-0">
        <table id="permisos-globales-table" class="table table-bordered table-striped table-sm mb-0" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th style="width:35px;">
                        <input type="checkbox" id="select-all-check" title="Seleccionar todos los visibles">
                    </th>
                    <th>Área</th>
                    <th>Artículo</th>
                    <th style="width:170px;">Frecuencia</th>
                    <th style="width:140px;">Cantidad</th>
                    <th style="width:120px;">Estatus</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.min.css">
<style>
    .ts-wrapper.form-control-sm .ts-control { min-height: unset; padding: .25rem .5rem; font-size: .875rem; }
    .ts-wrapper.form-control-sm .ts-control input { font-size: .875rem; }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {

    var selectedIds = new Set();

    // ── Tom Select — Buscador de artículos ───────────────────────────────────
    var tsArticulo = new TomSelect('#filter-articulo', {
        valueField: 'id',
        labelField: 'text',
        searchField: 'text',
        placeholder: 'Buscar artículo...',
        preload: true,
        plugins: ['clear_button'],
        load: function (query, callback) {
            $.getJSON('{{ route("permisos-cli.articulos") }}', { q: query })
                .done(function (data) { callback(data.results); })
                .fail(function () { callback(); });
        },
        render: {
            no_results: function () { return '<div class="no-results">Sin resultados</div>'; }
        }
    });

    // ── DataTable ─────────────────────────────────────────────────────────────
    var table = $('#permisos-globales-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("permisos-cli.data") }}',
            type: 'POST',
            data: function (d) {
                d._token          = '{{ csrf_token() }}';
                d.filter_area     = $('#filter-area').val();
                d.filter_articulo = tsArticulo.getValue();
                d.filter_status   = $('#filter-status').val();
            }
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                render: function (data, type, row) {
                    var checked = selectedIds.has(row.Clave) ? 'checked' : '';
                    return '<input type="checkbox" class="row-check" data-id="' + row.Clave + '" ' + checked + '>';
                }
            },
            { data: 'Area' },
            { data: 'Articulo' },
            {
                data: 'Frecuencia', orderable: false,
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    return '<div class="input-group input-group-sm">'
                         + '<input type="number" class="form-control update-frecuencia" data-id="' + row.Clave + '" value="' + data + '" min="0" max="360">'
                         + '<div class="input-group-append"><span class="input-group-text">días</span></div>'
                         + '</div>';
                }
            },
            {
                data: 'Cantidad', orderable: false,
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    return '<div class="input-group input-group-sm">'
                         + '<input type="number" class="form-control update-cantidad" data-id="' + row.Clave + '" value="' + data + '" min="0" max="999">'
                         + '<div class="input-group-append"><span class="input-group-text">cant.</span></div>'
                         + '</div>';
                }
            },
            {
                data: 'Estatus', orderable: false,
                render: function (data, type, row) {
                    if (type !== 'display') return data;
                    var cls  = data === 'Alta' ? 'btn-success' : 'btn-danger';
                    var icon = data === 'Alta' ? 'fa-lock-open' : 'fa-lock';
                    return '<button class="btn btn-xs ' + cls + ' toggle-status-global" data-id="' + row.Clave + '" data-status="' + data + '">'
                         + '<i class="fas ' + icon + '"></i> ' + data + '</button>';
                }
            }
        ],
        language: {
            processing: "Procesando...", search: "Buscar:", lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Sin registros", zeroRecords: "No se encontraron resultados",
            paginate: { first: "Primero", previous: "Anterior", next: "Siguiente", last: "Último" }
        },
        pageLength: 50,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        order: [[1, 'asc'], [2, 'asc']],
        scrollX: true,
        drawCallback: function () {
            $('#permisos-globales-table tbody .row-check').each(function () {
                if (selectedIds.has(parseInt($(this).data('id')))) $(this).prop('checked', true);
            });
            syncSelectAllState();
        }
    });

    // ── Filtros ───────────────────────────────────────────────────────────────
    $('#btn-filtrar').on('click', function () {
        selectedIds.clear();
        updateBulkPanel();
        table.ajax.reload();
    });

    $('#btn-limpiar').on('click', function () {
        $('#filter-area, #filter-status').val(function() { return this.options[0].value; });
        tsArticulo.clear();
        tsArticulo.clearOptions();
        tsArticulo.load('');
        selectedIds.clear();
        updateBulkPanel();
        table.ajax.reload();
    });

    // ── Selección ─────────────────────────────────────────────────────────────
    $('#permisos-globales-table').on('change', '.row-check', function () {
        var id = parseInt($(this).data('id'));
        if ($(this).is(':checked')) selectedIds.add(id);
        else selectedIds.delete(id);
        updateBulkPanel();
        syncSelectAllState();
    });

    $('#select-all-check').on('change', function () {
        var checked = $(this).is(':checked');
        $('#permisos-globales-table tbody .row-check').each(function () {
            var id = parseInt($(this).data('id'));
            $(this).prop('checked', checked);
            if (checked) selectedIds.add(id);
            else selectedIds.delete(id);
        });
        updateBulkPanel();
    });

    $('#bulk-deselect').on('click', function () {
        selectedIds.clear();
        $('#permisos-globales-table tbody .row-check').prop('checked', false);
        $('#select-all-check').prop('checked', false);
        updateBulkPanel();
    });

    function syncSelectAllState() {
        var total   = $('#permisos-globales-table tbody .row-check').length;
        var checked = $('#permisos-globales-table tbody .row-check:checked').length;
        $('#select-all-check').prop('indeterminate', checked > 0 && checked < total);
        $('#select-all-check').prop('checked', total > 0 && checked === total);
    }

    function updateBulkPanel() {
        var count = selectedIds.size;
        if (count > 0) {
            $('#bulk-count').text(count + ' seleccionado' + (count > 1 ? 's' : ''));
            $('#bulk-panel').slideDown(150);
        } else {
            $('#bulk-panel').slideUp(150);
        }
    }

    // ── Aplicar edición masiva ────────────────────────────────────────────────
    $('#bulk-apply').on('click', function () {
        if (selectedIds.size === 0) return;

        var frecuencia = $('#bulk-frecuencia').val();
        var cantidad   = $('#bulk-cantidad').val();
        var status     = $('#bulk-status').val();

        if (frecuencia === '' && cantidad === '' && status === '') {
            Swal.fire('Atención', 'Ingresa al menos un valor para aplicar.', 'warning');
            return;
        }

        Swal.fire({
            title: '¿Confirmar cambio masivo?',
            html: 'Se modificarán <strong>' + selectedIds.size + '</strong> permiso(s).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Sí, aplicar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            // Actualizar cada permiso individualmente usando la ruta existente
            var promises = [];
            selectedIds.forEach(function (id) {
                if (frecuencia !== '') {
                    promises.push($.post('/update-permiso-articulo/' + id, {
                        field: 'Frecuencia', value: frecuencia, _token: '{{ csrf_token() }}'
                    }));
                }
                if (cantidad !== '') {
                    promises.push($.post('/update-permiso-articulo/' + id, {
                        field: 'Cantidad', value: cantidad, _token: '{{ csrf_token() }}'
                    }));
                }
                if (status !== '') {
                    promises.push($.post('/update-permiso-articulo/' + id, {
                        field: 'Status', value: status, _token: '{{ csrf_token() }}'
                    }));
                }
            });

            $.when.apply($, promises).then(function () {
                Swal.fire('Listo', selectedIds.size + ' permisos actualizados correctamente.', 'success').then(function () {
                    selectedIds.clear();
                    updateBulkPanel();
                    $('#bulk-frecuencia, #bulk-cantidad').val('');
                    $('#bulk-status').val('');
                    table.ajax.reload(null, false);
                });
            }).fail(function () {
                Swal.fire('Error', 'Algunos permisos no pudieron actualizarse.', 'error');
                table.ajax.reload(null, false);
            });
        });
    });

    // ── Edición inline — Frecuencia ───────────────────────────────────────────
    $('#permisos-globales-table').on('change', '.update-frecuencia', function () {
        var id = $(this).data('id'), value = $(this).val();
        $.post('/update-permiso-articulo/' + id, { field: 'Frecuencia', value: value, _token: '{{ csrf_token() }}' })
            .done(function () { toastr.success('Frecuencia actualizada'); })
            .fail(function () { toastr.error('Error al actualizar'); });
    });

    // ── Edición inline — Cantidad ─────────────────────────────────────────────
    $('#permisos-globales-table').on('change', '.update-cantidad', function () {
        var id = $(this).data('id'), value = $(this).val();
        $.post('/update-permiso-articulo/' + id, { field: 'Cantidad', value: value, _token: '{{ csrf_token() }}' })
            .done(function () { toastr.success('Cantidad actualizada'); })
            .fail(function () { toastr.error('Error al actualizar'); });
    });

    // ── Toggle estatus inline ─────────────────────────────────────────────────
    $('#permisos-globales-table').on('click', '.toggle-status-global', function () {
        var id        = $(this).data('id');
        var newStatus = $(this).data('status') === 'Alta' ? 'Baja' : 'Alta';
        $.post('/toggle-status-permiso-articulo/' + id, { status: newStatus, _token: '{{ csrf_token() }}' })
            .done(function () {
                toastr.success('Estatus actualizado');
                table.ajax.reload(null, false);
            })
            .fail(function () { toastr.error('Error al actualizar estatus'); });
    });

});
</script>
@stop
