@extends('adminlte::page')

@section('usermenu_body')
<center><b>Administrador</b></center>
@stop

@section('title', __('Monitoreo VM'))

@section('content_header')
    
@stop

@section('content')

    <div class="container">
    <div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Producto más consumido</span>
                <span class="info-box-number" id="producto-mas-consumido">Cargando...</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-industry"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Planta de Alto Consumo</span>
                <span class="info-box-number" id="area-alto-consumo">Cargando...</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-power-off"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Vendings Activas</span>
                <span class="info-box-number" id="vendings-activas">Cargando...</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-dark"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Artículos Consumidos</span>
                <span class="info-box-number" id="articulos-consumidos">Cargando...</span>
            </div>
        </div>
    </div>
</div>
        <div class="row">
            <div class="col-8">

            
                <div class="card card-tabs">
                    <div class="card-header ">
                    <div id="tabs-container"></div>
                    </div>
                    <div class="card-body">
                    
                    <div id="tabs-content" class="container">
                        
                    </div>
                    </div>
                </div>
                <div class="card card-urvina-sync shadow-lg border-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                    <div class="pulse-dot mr-2"></div>
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sync-alt mr-2"></i> Sincronización de Máquinas Vending
                    </h5>
                    </div>

                    <div class="card-tools d-flex align-items-center">
                    <div class="custom-control custom-switch mr-2" title="Auto-actualizar cada 60s">
                        <input type="checkbox" class="custom-control-input" id="autoRefreshToggle">
                        <label class="custom-control-label text-white-50" for="autoRefreshToggle">Auto</label>
                    </div>

                    <button type="button" class="btn btn-tool" id="refreshSyncBtn" title="Actualizar ahora">
                        <i class="fas fa-redo"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Colapsar">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="maximize" title="Pantalla completa">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- KPIs -->
                    <div class="row text-center mb-3">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="kpi-box kpi-total">
                        <div class="kpi-label">Total</div>
                        <div class="kpi-value" id="kpiTotal">—</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="kpi-box kpi-ok">
                        <div class="kpi-label">En línea</div>
                        <div class="kpi-value" id="kpiOk">—</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="kpi-box kpi-warn">
                        <div class="kpi-label">Con retraso</div>
                        <div class="kpi-value" id="kpiWarn">—</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="kpi-box kpi-crit">
                        <div class="kpi-label">Sin sincronizar</div>
                        <div class="kpi-value" id="kpiCrit">—</div>
                        </div>
                    </div>
                    </div>

                    <!-- Salud global -->
                    <div class="mb-3">
                    <small class="text-white-50">Salud de sincronización</small>
                    <div class="progress" style="height:12px;">
                        <div id="syncHealthBar" class="progress-bar bg-success" role="progressbar" style="width:0%"></div>
                    </div>
                    </div>

                    <!-- Filtros rápidos -->
                    <div class="form-row mb-3">
                    <div class="col-sm-6 mb-2">
                        <input id="syncSearch" class="form-control" placeholder="Buscar por cliente, base o VM…">
                    </div>
                    <div class="col-sm-3 mb-2">
                        <select id="statusFilter" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="ok">En línea</option>
                        <option value="warn">Con retraso</option>
                        <option value="crit">Sin sincronizar</option>
                        </select>
                    </div>
                    <div class="col-sm-3 mb-2">
                        <select id="plantFilter" class="form-control">
                        <option value="">Todas las plantas</option>
                        </select>
                    </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive" style="max-height:460px; overflow:auto;">
                    <table class="table table-hover table-borderless mb-0" id="syncTable">
                        <thead class="thead-dark sticky-top">
                        <tr>
                            <th>Cliente</th>
                            <th>Base Suscriptor</th>
                            <th>Última sincronización</th>
                            <th class="text-center">Hace</th>
                            <th class="text-center">Planta</th>
                            <th class="text-center">VM</th>
                            <th class="text-center">Estado</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-white-50">Actualizado: <span id="lastUpdated">—</span></small>
                    <small class="text-white-50">Umbrales: OK ≤ 15 min · Retraso ≤ 60 min · Crítico &gt; 60 min</small>
                </div>
                </div>
            
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Gráficas
                            </h3><br><br>
                            <div class="card-tools">
                                <ul class="nav nav-pills ml-auto" id="chartPills">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-chart="planta" href="#">Por Planta</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-chart="productos" href="#">Top Productos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-chart="matriz" href="#">Matriz</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-chart="dia" href="#">Por Día</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="chart-planta" class="chart-container">
                                <h5 class="text-center">Consumo Total por Planta</h5>
                                <div id="chartPlantas" style="height: 400px; margin-bottom: 30px;"></div>
                            </div>
                            <div id="chart-productos" class="chart-container d-none">
                                <h5 class="text-center">Top 5 Productos</h5>
                                <div id="chartTopProductos" style="height: 400px; margin-bottom: 30px;"></div>
                            </div>
                            <div id="chart-matriz" class="chart-container d-none">
                                <h5 class="text-center">Consumo por Planta y Producto</h5>
                                <div id="chartMatriz" style="height: 400px; margin-bottom: 30px;"></div>
                            </div>
                            <div id="chart-dia" class="chart-container d-none">
                                <h5 class="text-center">Consumo Diario</h5>
                                <div id="chartPorDia" style="height: 400px; margin-bottom: 30px;"></div>
                            </div>
                            </div>
                            </div>
                        
                    
                </div>





            <div class="col-4">
                <div class="row">
                    <div class="col">
                        
                        <div class="card">

                            <div class="card-header">

                                <h5 class="card-title">
                                    <i class="fas fa-history mr-1"></i>
                                    Consumos Recientes
                                </h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-tool dropdown-toggle"
                                            data-toggle="dropdown">
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
                            <div class="card-body p-0 table-responsive" style="height:740px">
                                <table class="table table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">Fecha</th>
                                            <th style="width: 40px">Empleado</th>
                                            <th>Producto</th>
                                            <th>VM</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-consumptions-body">
                                        <!-- Datos dinámicos aquí -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                 <div class="col">
                       <!-- Se puede agregar una card aqui -->
                    </div>
            </div>
        </div>
    </div>

    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
    <style>
        
        .vending-machine {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/vendor/adminlte/dist/img/vending-machine.png') no-repeat center center;
            background-size: contain;
            z-index: 2;
        }

        
        .fill {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0;
            background-color: green;
            z-index: 1;
            transition: height 1s ease;
            margin-bottom: 2px
        }

        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: flex;
        }

        .percentage {
            margin-top: 20px;
            font-size: 24px;
        }

        .vm-container {
            position: relative;
            width: 75px;
            /* Ajusta el tamaño según tus necesidades */
            height: 99px;
            /* Ajusta el tamaño según tus necesidades */
        }
        
        
        .tab {
            cursor: pointer;
            padding: 10px;
            margin-right: 5px;
            border: 1px solid #ccc;
            display: inline-block;
            background-color: #f1f1f1;
        }
        .tab.active {
            background-color: #ddd;
        }
        .vm-container.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .vm-container {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .fill {
            width: 100%;
            background-color: red;
            border-radius: 5px;
        }
        .disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    
    

/* Tema llamativo */
.card-urvina-sync{
  background: linear-gradient(135deg,#0e1b2d 0%, #11344b 40%, #0e1b2d 100%);
  color:#e8f1f7;
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.05) inset;
  overflow: hidden;
}
.card-urvina-sync .card-header{
  background: linear-gradient(90deg,#0ea5a5, #0ea56a);
  color:#fff;
  border-bottom: none;
}
.card-urvina-sync .card-footer{
  background: transparent;
  border-top: 1px dashed rgba(255,255,255,.15);
}
.pulse-dot{
  width:10px;height:10px;border-radius:50%;
  background:#9cf6ff; box-shadow:0 0 0 0 rgba(156,246,255,.7);
  animation: pulse 2s infinite;
}
@keyframes pulse{
  0%{transform:scale(.95); box-shadow:0 0 0 0 rgba(156,246,255,.7)}
  70%{transform:scale(1); box-shadow:0 0 0 10px rgba(156,246,255,0)}
  100%{transform:scale(.95); box-shadow:0 0 0 0 rgba(156,246,255,0)}
}

/* KPIs */
.kpi-box{
  border-radius: 16px;
  padding:.6rem 1rem;
  background: rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.1);
  backdrop-filter: blur(4px);
}
.kpi-label{font-size:.8rem;opacity:.8}
.kpi-value{font-size:1.4rem;font-weight:700;letter-spacing:.3px}
.kpi-total{box-shadow: inset 0 0 16px rgba(255,255,255,.05)}
.kpi-ok{border-color:rgba(40,167,69,.55)}
.kpi-warn{border-color:rgba(255,193,7,.65)}
.kpi-crit{border-color:rgba(220,53,69,.65)}

/* Tabla */
#syncTable tbody tr td{vertical-align: middle;}
.badge-dot{
  display:inline-flex;align-items:center;padding:.35rem .6rem;border-radius:20px;font-weight:600;
}
.badge-dot i{font-size:.6rem;margin-right:.4rem}
.badge-ok{background:rgba(40,167,69,.15); color:#a4ffb1; border:1px solid rgba(40,167,69,.45)}
.badge-warn{background:rgba(255,193,7,.12); color:#ffe08a; border:1px solid rgba(255,193,7,.45)}
.badge-crit{background:rgba(220,53,69,.15); color:#ffb3be; border:1px solid rgba(220,53,69,.45)}
.thead-dark th{position: sticky; top:0; z-index: 1;}
/* Inputs */
#syncSearch, #statusFilter, #plantFilter{background:#0f2536;border:1px solid rgba(255,255,255,.15);color:#e8f1f7}
#syncSearch::placeholder{color:#a6bdc9}
</style>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script>
const SYNC_THRESHOLDS_MIN = { ok: 15, warn: 60 }; // minutos

let syncData = []; // variable global para guardar la última data
let autoTimer = null;

const $tbody = document.querySelector('#syncTable tbody');
const $kpiTotal = document.getElementById('kpiTotal');
const $kpiOk = document.getElementById('kpiOk');
const $kpiWarn = document.getElementById('kpiWarn');
const $kpiCrit = document.getElementById('kpiCrit');
const $bar = document.getElementById('syncHealthBar');
const $updated = document.getElementById('lastUpdated');

const $search = document.getElementById('syncSearch');
const $statusFilter = document.getElementById('statusFilter');
const $plantFilter = document.getElementById('plantFilter');
const $refreshBtn = document.getElementById('refreshSyncBtn');
const $auto = document.getElementById('autoRefreshToggle');

// === Llamada AJAX al controlador ===
function fetchSyncData(){
  return $.post('{{ route('sync.data') }}', { _token: '{{ csrf_token() }}' })
    .then(function(res){
      if (!res || res.ok !== true || !Array.isArray(res.data)) {
        throw new Error(res && res.message ? res.message : 'Respuesta inválida del servidor');
      }
      return res.data;
    });
}

function parseDate(s){ return new Date(s.replace(' ', 'T')); }
function minutesDiff(d){ return Math.floor((new Date() - d) / 60000); }
function timeAgoES(min){
  if (min < 1) return 'ahora';
  if (min < 60) return `hace ${min} min`;
  const h = Math.floor(min/60);
  const m = min % 60;
  return m ? `hace ${h}h ${m}m` : `hace ${h}h`;
}
function statusFromMinutes(min){
  if (min <= SYNC_THRESHOLDS_MIN.ok) return 'ok';
  if (min <= SYNC_THRESHOLDS_MIN.warn) return 'warn';
  return 'crit';
}
function iconForStatus(s){
  return s==='ok' ? 'fas fa-check-circle' :
         s==='warn' ? 'fas fa-exclamation-circle' :
                      'fas fa-times-circle';
}

function fillPlantFilter(data){
  const unique = [...new Set(data.map(r=>r.Id_Planta))].sort((a,b)=>a-b);
  $plantFilter.innerHTML =
    '<option value="">Todas las plantas</option>' +
    unique.map(p=>`<option value="${p}">Planta ${p}</option>`).join('');
}

function renderTable(data){
  const q = ($search.value||'').toLowerCase().trim();
  const byStatus = $statusFilter.value;
  const byPlant = $plantFilter.value;

  const filtered = data.filter(r=>{
    const d = parseDate(r.Ultima_Sincronizacion);
    const min = minutesDiff(d);
    const st = statusFromMinutes(min);
    r.__minutes=min; r.__status=st; r.__date=d;
    const text = `${r.cliente} ${r.Base_Datos_Suscriptor} ${r.Id_Maquina}`.toLowerCase();
    const okSearch = !q || text.includes(q);
    const okStatus = !byStatus || st===byStatus;
    const okPlant = !byPlant || String(r.Id_Planta)===String(byPlant);
    return okSearch && okStatus && okPlant;
  });

  // KPIs
  const total = data.length;
  const ok = data.filter(x=>statusFromMinutes(minutesDiff(parseDate(x.Ultima_Sincronizacion)))==='ok').length;
  const warn = data.filter(x=>statusFromMinutes(minutesDiff(parseDate(x.Ultima_Sincronizacion)))==='warn').length;
  const crit = total - ok - warn;

  $kpiTotal.textContent = total;
  $kpiOk.textContent = ok;
  $kpiWarn.textContent = warn;
  $kpiCrit.textContent = crit;

  const health = total ? Math.round((ok/total)*100) : 0;
  $bar.style.width = health+'%';
  $bar.className = 'progress-bar ' + (health>80?'bg-success':health>50?'bg-warning':'bg-danger');
  $updated.textContent = new Date().toLocaleString();

  // filas
  $tbody.innerHTML = filtered.map(r=>{
    const ago = timeAgoES(r.__minutes);
    const st = r.__status;
    const badgeClass = st==='ok'?'badge-ok':(st==='warn'?'badge-warn':'badge-crit');
    return `
      <tr class="row-${st}">
        <td><strong>${r.cliente}</strong></td>
        <td><code>${r.Base_Datos_Suscriptor}</code></td>
        <td><span title="${r.Ultima_Sincronizacion}">${r.__date.toLocaleString()}</span></td>
        <td class="text-center">${ago}</td>
        <td class="text-center"><span class="badge badge-info">#${r.Id_Planta}</span></td>
        <td class="text-center"><span class="badge badge-primary">VM ${r.Id_Maquina}</span></td>
        <td class="text-center">
          <span class="badge-dot ${badgeClass}">
            <i class="${iconForStatus(st)}"></i> ${st==='ok'?'En línea':st==='warn'?'Retraso':'Crítico'}
          </span>
        </td>
      </tr>
    `;
  }).join('');
}

function initSyncCard(){
  refreshNow();

  [$search,$statusFilter,$plantFilter].forEach(el=>
    el.addEventListener('input', ()=>renderTable(syncData))
  );
  $refreshBtn.addEventListener('click', ()=>refreshNow());
  $auto.addEventListener('change', ()=>{
    if($auto.checked){
      autoTimer = setInterval(refreshNow, 60000);
    }else{
      clearInterval(autoTimer);
    }
  });
}

function refreshNow(){
  // Opcional: feedback visual mientras carga
  $tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">
    <i class="fas fa-circle-notch fa-spin"></i> Cargando...
  </td></tr>`;

  fetchSyncData()
    .then(data=>{
      syncData = data;
      fillPlantFilter(syncData);
      renderTable(syncData);
    })
    .catch(err=>{
      console.error('Error cargando datos de sincronización:', err);
      $tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">
        <i class="fas fa-exclamation-triangle"></i> Error al cargar: ${ (err && err.message) ? err.message : 'desconocido' }
      </td></tr>`;
      // Opcional: reset KPIs
      [$kpiTotal,$kpiOk,$kpiWarn,$kpiCrit].forEach(el=>el.textContent='—');
      $bar.style.width='0%'; $bar.className='progress-bar bg-danger';
      $updated.textContent = new Date().toLocaleString();
    });
}

document.addEventListener('DOMContentLoaded', initSyncCard);
</script>

    <script>
Highcharts.setOptions({
    lang: {
        contextButtonTitle: "Opciones de exportación",
        downloadPNG: "Descargar imagen PNG",
        downloadJPEG: "Descargar imagen JPEG",
        downloadPDF: "Descargar PDF",
        downloadSVG: "Descargar SVG",
        downloadCSV: "Descargar CSV",
        downloadXLS: "Descargar Excel",
        viewData: "Ver datos en tabla",
        viewFullscreen: "Ver en pantalla completa",
        exitFullscreen: "Salir de pantalla completa",
        printChart: "Imprimir gráfica",
        loading: "Cargando...",
        noData: "No hay datos para mostrar"
    }
});
</script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Control de tabs manual
        $('#chartPills a').on('click', function (e) {
            e.preventDefault();

            // Cambiar clase activa
            $('#chartPills a').removeClass('active');
            $(this).addClass('active');

            const chartTarget = $(this).data('chart');

            // Ocultar todos los contenedores
            $('.chart-container').addClass('d-none');

            // Mostrar el seleccionado
            $('#chart-' + chartTarget).removeClass('d-none');

            // Redibujar la gráfica si es necesario
            switch (chartTarget) {
                case 'planta':
                    if (window.chartPlantas) chartPlantas.resize();
                    break;
                case 'productos':
                    if (window.chartTopProductos) chartTopProductos.resize();
                    break;
                case 'matriz':
                    if (window.chartMatriz) chartMatriz.resize();
                    break;
                case 'dia':
                    if (window.chartPorDia) chartPorDia.resize();
                    break;
            }
        });
    });
</script>

</script>
    

    <script>
        function updateInfoBoxes() {
    fetch('/vm-admin')
        .then(response => response.json())
        .then(data => {
            document.getElementById('producto-mas-consumido').innerText = data.producto_mas_consumido || 'N/A';
            document.getElementById('area-alto-consumo').innerText = data.planta_alto_consumo || 'N/A';
            document.getElementById('vendings-activas').innerText = data.vendings_activas || '0';
            document.getElementById('articulos-consumidos').innerText = data.articulos_consumidos || '0';
        })
        .catch(error => console.error('Error:', error));
}

setInterval(updateInfoBoxes, 5000); // Actualizar cada 5 segundos
    </script>
    <script>
    async function fetchData() {
        const response = await fetch('/vm-admingraphs');
        return await response.json();
    }

    function updateCharts(data) {
        // 1. Consumo por planta
        if (data.porPlanta?.length) {
            Highcharts.chart('chartPlantas', {
                chart: { type: 'column' },
                title: { text: 'Consumo por Planta' },
                accessibility: { enabled: false },
                xAxis: { categories: data.porPlanta.map(p => p.planta) },
                yAxis: {
                    min: 0,
                    title: { text: 'Total Consumido' }
                },
                series: [{
                    name: 'Consumo',
                    data: data.porPlanta.map(p => Number(p.total_consumo) || 0)
                }]
            });
        }

        // 2. Top productos (pastel)
        if (data.topProductos?.length) {
            Highcharts.chart('chartTopProductos', {
                chart: { type: 'pie' },
                title: { text: 'Top Productos Consumidos' },
                accessibility: { enabled: false },
                series: [{
                    name: 'Total',
                    colorByPoint: true,
                    data: data.topProductos.map(p => ({
                        name: p.producto,
                        y: Number(p.total) || 0
                    }))
                }]
            });
        }

        // 3. Matriz planta-producto (barras apiladas)
        if (data.porPlantaYProducto?.length) {
            const productos = [...new Set(data.porPlantaYProducto.map(d => d.producto))];
            const plantas = [...new Set(data.porPlantaYProducto.map(d => d.planta))];
            const colores = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

            const datasets = plantas.map((planta, idx) => {
                const datos = productos.map(prod => {
                    const found = data.porPlantaYProducto.find(d => d.planta === planta && d.producto === prod);
                    return found ? Number(found.total) : 0;
                });
                return {
                    name: planta,
                    data: datos,
                    color: colores[idx % colores.length]
                };
            });

            Highcharts.chart('chartMatriz', {
                chart: { type: 'column' },
                title: { text: 'Consumo por Planta y Producto' },
                accessibility: { enabled: false },
                xAxis: { categories: productos },
                yAxis: {
                    min: 0,
                    title: { text: 'Total Consumido' },
                    stackLabels: {
                        enabled: true,
                        formatter: function () {
                            return this.total;
                        }
                    }
                },
                plotOptions: {
                    column: { stacking: 'normal' }
                },
                series: datasets
            });
        }

        // 4. Consumo por día (línea)
        if (data.porDia?.length) {
            Highcharts.chart('chartPorDia', {
                chart: { type: 'line' },
                title: { text: 'Consumo Diario Total' },
                accessibility: { enabled: false },
                xAxis: { categories: data.porDia.map(d => d.dia) },
                yAxis: {
                    title: { text: 'Total Diario' },
                    min: 0
                },
                series: [{
                    name: 'Total Diario',
                    data: data.porDia.map(d => Number(d.total) || 0),
                    marker: { enabled: true }
                }]
            });
        }
    }

    function refreshCharts() {
        fetchData().then(data => updateCharts(data));
    }

    refreshCharts();
    setInterval(refreshCharts, 60000); // cada minuto
</script>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <script>
        let vendingMachinesData = [];

        function updateVendingMachine(id, percentage) {
            const fillElement = document.getElementById(`fill-${id}`);
            const percentageElement = document.getElementById(`percentage-${id}`);

            percentageElement.textContent = percentage + '%';

            // Actualizar el color de la barra de llenado según el porcentaje
            if (percentage >= 71) {
                fillElement.style.backgroundColor = 'green';
            } else if (percentage >= 51) {
                fillElement.style.backgroundColor = 'yellow';
            } else {
                fillElement.style.backgroundColor = 'red';
            }

            // Actualizar la altura de la barra de llenado
            fillElement.style.height = percentage + '%';
        }

        function initializeTabs(currentActiveTab = null) {
            const tabsContainer = document.getElementById('tabs-container');
            const tabsContent = document.getElementById('tabs-content');

            tabsContainer.innerHTML = '';
            tabsContent.innerHTML = '';

            const plants = [...new Set(vendingMachinesData.map(machine => machine.planta))];

            plants.forEach(plant => {
                const machineCount = vendingMachinesData.filter(m => m.planta === plant).length;

                const tab = document.createElement('div');
                tab.classList.add('tab');
                tab.textContent = `${plant} (${machineCount})`;
                tab.addEventListener('click', () => showTab(plant));
                tabsContainer.appendChild(tab);

                const tabContent = document.createElement('div');
                tabContent.classList.add('tab-content', 'row');
                tabContent.id = `tab-content-${plant}`;
                tabsContent.appendChild(tabContent);
            });

            // Restaurar la pestaña activa anterior, si existe
            if (plants.length > 0) {
                const tabToShow = currentActiveTab && plants.includes(currentActiveTab) ? currentActiveTab : plants[0];
                showTab(tabToShow);
            }
        }

    function showTab(plant) {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.classList.remove('active');
            const tabName = tab.textContent.replace(/\s\(\d+\)$/, '');
            if (tabName === plant) {
                tab.classList.add('active');
            }
        });

        tabContents.forEach(content => {
            content.classList.remove('active');
            content.style.display = 'none'; // 👈 Ocultamos todos
        });

        const activeContent = document.getElementById(`tab-content-${plant}`);
        activeContent.classList.add('active');
        activeContent.style.display = 'flex'; // 👈 Mostramos solo el actual

        renderMachines(plant);
    }

        function renderMachines(plant) {
            const tabContent = document.getElementById(`tab-content-${plant}`);
            tabContent.innerHTML = ''; // Limpiar contenido previo

            const machines = vendingMachinesData.filter(machine => machine.planta === plant);

            machines.forEach(machine => {
                const vmContainer = document.createElement('div');
                vmContainer.classList.add('vm-container');

                const fillDiv = document.createElement('div');
                fillDiv.classList.add('fill');
                fillDiv.id = `fill-${machine.id}`;

                const vendingMachineDiv = document.createElement('div');
                vendingMachineDiv.classList.add('vending-machine');

                const percentageDiv = document.createElement('div');
                percentageDiv.classList.add('percentage');
                percentageDiv.id = `percentage-${machine.id}`;
                percentageDiv.textContent = machine.percentage + '% ';

                const NameVMDiv = document.createElement('div');
                NameVMDiv.textContent = machine.nombrevm;

                vmContainer.appendChild(fillDiv);
                vmContainer.appendChild(vendingMachineDiv);

                const colDiv = document.createElement('div');
                colDiv.classList.add('col-4');

                const cardDiv = document.createElement('div');
                cardDiv.classList.add('card');
                if (machine.dispo !== "On") {
                    cardDiv.classList.add('bg-danger');
                    cardDiv.classList.add('disabled');
                }
                console.log(`Máquina ${machine.nombrevm} - dispo: ${machine.dispo}`);

                const rowDiv = document.createElement('div');
                rowDiv.classList.add('row');

                const centerDiv = document.createElement('center');
                centerDiv.appendChild(vmContainer);

                const innerColDiv = document.createElement('div');
                innerColDiv.classList.add('col');
                innerColDiv.appendChild(centerDiv);

                const percentageColDiv = document.createElement('div');
                percentageColDiv.classList.add('col');
                percentageColDiv.appendChild(percentageDiv);
                percentageColDiv.appendChild(NameVMDiv);

                rowDiv.appendChild(innerColDiv);
                rowDiv.appendChild(percentageColDiv);

                cardDiv.appendChild(rowDiv);

                colDiv.appendChild(cardDiv);

                tabContent.appendChild(colDiv);

                updateVendingMachine(machine.id, machine.percentage);
            });
        }

        function fetchVendingMachinesData() {
            const currentActiveTab = document.querySelector('.tab.active')?.textContent.replace(/\s\(\d+\)$/, '');
            
            $.getJSON('/vm-allstatus', function(data) {
                vendingMachinesData = data.map(stat => ({
                    planta: stat.Nplanta,
                    id: stat.Id_Maquina,
                    percentage: parseInt(stat.Per_Alm), // Ajusta esto según tu lógica
                    nombrevm: stat.NameVM,
                    dispo: stat.dispo
                }));

                console.log(vendingMachinesData);

            
                // Inicializar las pestañas pasando la actual activa
                initializeTabs(currentActiveTab);
            });
        }

        // Inicializar las máquinas expendedoras al cargar la página
        fetchVendingMachinesData();

        // Actualizar las máquinas expendedoras cada 5 segundos con datos reales
        setInterval(fetchVendingMachinesData, 60000);
    </script>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fetchRecentConsumptions() {
                fetch('/vm-rallconsum')
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.getElementById('recent-consumptions-body');
                        tbody.innerHTML = ''; // Clear existing content
                        data.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.FechaHumana}</td>
                                <td>${item.NombreEmpleado}</td>
                                <td>${item.NArticulo}</td>
                                <td>${item.NombreMaquina}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error fetching recent consumptions:', error));
            }

            // Fetch data initially
            fetchRecentConsumptions();
            
            // Fetch data every minute
            setInterval(fetchRecentConsumptions, 60000); // 60000 ms = 1 minute
        });
    </script>
   
@stop
