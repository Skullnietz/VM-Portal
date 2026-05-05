@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Plantas de VMs'))

@section('content_header')
<div class="container">
    <div class="row align-items-center">
        <div class="col-9">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;{{ __('Plantas de VMs') }}
            </h4>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container">
    <div id="loadingState" class="text-center py-5">
        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
        <p class="mt-2 text-muted">Cargando vendings…</p>
    </div>

    <div id="emptyState" class="text-center py-5" style="display:none;">
        <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
        <p class="text-muted">No hay vendings asignadas a tu cuenta.</p>
    </div>

    <div id="plantasContainer"></div>
</div>
@stop

@section('right-sidebar')
@stop

@section('css')
<style>
    /* ── Plant header ────────────────────────────────────── */
    .plant-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        background: linear-gradient(90deg, #1a3a5c 0%, #2d6a9f 100%);
        color: #fff;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
        user-select: none;
    }
    .plant-header:hover { filter: brightness(1.08); }
    .plant-header img {
        width: 46px; height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255,255,255,.5);
        flex-shrink: 0;
    }
    .plant-header .plant-name { font-size: 1.05rem; font-weight: 700; flex: 1; }
    .plant-header .plant-stats {
        display: flex; gap: 16px; font-size: .78rem; opacity: .9;
    }
    .plant-header .stat-item { text-align: center; }
    .plant-header .stat-value { font-size: 1.1rem; font-weight: 700; display: block; }
    .plant-header .chevron { transition: transform .25s; }
    .plant-header.collapsed .chevron { transform: rotate(-90deg); }

    /* ── Vending card ────────────────────────────────────── */
    .vending-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 16px;
        padding: 18px;
        background: #f4f6f9;
        border-radius: 0 0 8px 8px;
    }
    .vending-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,.08);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .vending-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,.13);
    }
    .vending-card-header {
        padding: 12px 14px 8px;
        border-bottom: 1px solid #f0f0f0;
    }
    .vending-card-header .vm-name {
        font-weight: 700; font-size: .95rem; color: #1a3a5c;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .vending-card-header .vm-series { font-size: .75rem; color: #888; }
    .vending-card-body { padding: 10px 14px 6px; }

    /* Fill bar */
    .fill-label {
        display: flex; justify-content: space-between;
        font-size: .75rem; color: #555; margin-bottom: 4px;
    }
    .fill-bar { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; }
    .fill-bar-inner {
        height: 100%; border-radius: 4px;
        transition: width .4s ease;
        background: #28a745;
    }
    .fill-bar-inner.fill-warn  { background: #ffc107; }
    .fill-bar-inner.fill-low   { background: #dc3545; }

    /* Sync row */
    .sync-row {
        display: flex; align-items: center; gap: 6px;
        font-size: .75rem; color: #666; margin-top: 8px;
    }
    .badge-online  { background: #28a745; color:#fff; padding:2px 8px; border-radius:12px; font-size:.7rem; }
    .badge-offline { background: #6c757d; color:#fff; padding:2px 8px; border-radius:12px; font-size:.7rem; }

    /* Actions */
    .vending-card-footer {
        padding: 10px 14px;
        display: flex; gap: 8px; border-top: 1px solid #f0f0f0;
    }
    .vending-card-footer .btn { flex: 1; font-size: .78rem; border-radius: 20px; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function goBack() { window.history.back(); }

// ── Helpers de sincronización (misma lógica que dashboard admin) ──────────────
// Umbral: ≤60 min = En línea, >60 min = Sin conexión
const SYNC_ONLINE_MIN = 60;

function parseDate(s) { return new Date(s.replace(' ', 'T')); }
function minutesDiff(d) { return Math.floor((new Date() - d) / 60000); }

function timeAgoES(min) {
    if (min < 1)  return 'ahora';
    if (min < 60) return `hace ${min} min`;
    const h = Math.floor(min / 60);
    const m = min % 60;
    return m ? `hace ${h}h ${m}m` : `hace ${h}h`;
}

function syncInfo(ultima_sync) {
    if (!ultima_sync) return { status: 'offline', ago: 'Sin sincronización', minutes: null };
    const dt  = parseDate(ultima_sync);
    const min = minutesDiff(dt);
    return {
        status:  min <= SYNC_ONLINE_MIN ? 'online' : 'offline',
        ago:     timeAgoES(min),
        minutes: min,
    };
}
// ─────────────────────────────────────────────────────────────────────────────

$(document).ready(function () {

    $.ajax({
        url: '/op/vendings/data',
        method: 'GET',
        success: function (plantas) {
            $('#loadingState').hide();

            if (!plantas || plantas.length === 0) {
                $('#emptyState').show();
                return;
            }

            const container = $('#plantasContainer');

            plantas.forEach(function (planta, idx) {
                const imgSrc  = planta.Ruta_Imagen || '/Images/Plantas/default.png';
                const avgFill = parseFloat(planta.avg_fill_pct).toFixed(1);

                // ── Plant block ──────────────────────────────────
                const block = $(`
                    <div class="mb-4">
                        <div class="plant-header" id="phdr-${idx}">
                            <img src="${imgSrc}" alt="${planta.Txt_Nombre_Planta}" onerror="this.src='/Images/Plantas/default.png'">
                            <span class="plant-name">${planta.Txt_Nombre_Planta}</span>
                            <div class="plant-stats d-none d-sm-flex">
                                <div class="stat-item">
                                    <span class="stat-value">${planta.total_vendings}</span>
                                    <span>Vendings</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value" id="online-count-${idx}">—</span>
                                    <span>En línea</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">${avgFill}%</span>
                                    <span>Relleno</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down chevron ml-2"></i>
                        </div>
                        <div class="vending-grid" id="pgrid-${idx}"></div>
                    </div>
                `);

                // Toggle collapse
                block.find(`#phdr-${idx}`).on('click', function () {
                    const grid = $(`#pgrid-${idx}`);
                    const isVisible = grid.is(':visible');
                    grid.slideToggle(200);
                    $(this).toggleClass('collapsed', !isVisible);
                });

                // ── Vending cards ────────────────────────────────
                const grid = block.find(`#pgrid-${idx}`);
                let onlineCount = 0;

                planta.vendings.forEach(function (vm) {
                    const fillPct   = parseFloat(vm.fill_pct).toFixed(1);
                    const fillClass = vm.fill_pct >= 60 ? '' : vm.fill_pct >= 30 ? 'fill-warn' : 'fill-low';
                    const sync      = syncInfo(vm.ultima_sync);

                    if (sync.status === 'online') onlineCount++;

                    const statusBadge = sync.status === 'online'
                        ? '<span class="badge-online"><i class="fas fa-circle mr-1" style="font-size:.5rem"></i>En línea</span>'
                        : '<span class="badge-offline"><i class="fas fa-circle mr-1" style="font-size:.5rem"></i>Sin conexión</span>';

                    const card = $(`
                        <div class="vending-card">
                            <div class="vending-card-header">
                                <div class="vm-name" title="${vm.Txt_Nombre}">${vm.Txt_Nombre}</div>
                                <div class="vm-series">${vm.Txt_Serie_Maquina} &bull; <span class="badge badge-info badge-sm" style="font-size:.65rem">${vm.Txt_Tipo_Maquina}</span></div>
                            </div>
                            <div class="vending-card-body">
                                <div class="fill-label">
                                    <span>Relleno</span>
                                    <span>${fillPct}% &bull; ${vm.total_stock}/${vm.total_capacity}</span>
                                </div>
                                <div class="fill-bar">
                                    <div class="fill-bar-inner ${fillClass}" style="width:${fillPct}%"></div>
                                </div>
                                <div class="sync-row">
                                    ${statusBadge}
                                    <span class="text-muted">${sync.ago}</span>
                                </div>
                            </div>
                            <div class="vending-card-footer">
                                <a href="corte/pre/${vm.Id_Maquina}" class="btn btn-sm btn-primary" title="Generar Corte">
                                    <i class="fas fa-cut mr-1"></i>Corte
                                </a>
                                <button class="btn btn-sm btn-warning check-missing-btn" data-id="${vm.Id_Maquina}" title="Descargar Faltantes">
                                    <i class="fas fa-clipboard-list mr-1"></i>Faltantes
                                </button>
                            </div>
                        </div>
                    `);

                    grid.append(card);
                });

                // Actualizar contador En línea en el header de la planta
                block.find(`#online-count-${idx}`).text(onlineCount);

                container.append(block);
            });

            // Faltantes download handler
            $(document).on('click', '.check-missing-btn', function () {
                window.location.href = `/op/vending/download-missing-items/${$(this).data('id')}`;
            });
        },
        error: function () {
            $('#loadingState').hide();
            $('#plantasContainer').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error al cargar los datos. Intenta recargar la página.
                </div>
            `);
        }
    });
});
</script>
@stop
