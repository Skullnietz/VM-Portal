@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Consulta Consumos'))

@section('content_header')
<div class="container">
  <div class="row">
    <div class=" col-md-9 col-9">
      <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Consulta de Consumos') }}</h4>
    </div>
    <div class="col-md-3 col-3 ml-auto">
    </div>


  </div>
</div>
@stop


@section('content')
  <style>
    :root {
      --urv-verde: #0E7E2D;
      --urv-azul: #0F1934
    }

    .card {
      background: #fff;
      border: 1px solid #e6eaf2;
      border-radius: 14px;
      box-shadow: 0 6px 16px rgba(15, 25, 52, .06)
    }

    .mini-progress {
      height: 10px;
      background: #f3f6fb;
      border-radius: 999px;
      overflow: hidden
    }

    .mini-progress>span {
      display: block;
      height: 100%
    }

    .bar-consumido {
      background: var(--urv-azul)
    }

    .bar-disponible {
      background: var(--urv-verde)
    }

    table.dataTable tbody tr.alerta>td {
      background: #fff4f4 !important;
    }

    .badge-chip {
      font-size: 11px;
      padding: 2px 8px;
      border-radius: 999px;
      border: 1px solid #d9e1ee;
      background: #f7f9fc
    }

    /* Barra unificada */
    .uni-bar {
      height: 12px;
      background: #eef3f9;
      border-radius: 999px;
      position: relative;
      overflow: hidden
    }

    .uni-bar .seg {
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0
    }

    .uni-bar .seg.consumido {
      background: #0F1934
    }

    .uni-bar .seg.disponible {
      background: #0E7E2D
    }

    .uni-bar .seg.excedido {
      background: #d63031
    }

    /* rojo para exceso */
    .uni-meta {
      font-size: 12px;
      color: #6b7280
    }

    /* Chip de estado */
    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 12px;
      border: 1px solid #e3e8f3
    }

    .chip.ok {
      background: #f2fbf5;
      color: #136f2b;
      border-color: #d7f0df
    }

    .chip.warn {
      background: #fff9ed;
      color: #a05b00;
      border-color: #ffe4b5
    }

    .chip.bad {
      background: #fff1f1;
      color: #9b1c1c;
      border-color: #ffd6d6
    }

    /* KPIs */
    .kpi {
      background: #fff;
      border: 1px solid #e6eaf2;
      border-radius: 12px;
      padding: 12px
    }

    .kpi .t {
      font-size: 12px;
      color: #6b7280
    }

    .kpi .v {
      font-size: 18px;
      font-weight: 600
    }

    .chart-h-110 {
      height: 200px;
    }

    /* Ajusta aquí la altura deseada */
    .chart-h-150 {
      height: 150px;
    }

    /* (opcional) otra altura */
    .chart-h-110 canvas,
    .chart-h-150 canvas {
      width: 100% !important;
      height: 100% !important;
      display: block;
    }
  </style>



  <!-- Tabla de consumos -->
  <div class="card p-3 mb-3">
    <h5 class="mb-3">Consulta de consumos por empleado</h5>

    <form id="filtro" class="row g-3">
      @csrf

      <div class="col-md-5">
        <label class="form-label">Empleado (buscar por número o nombre)</label>
        <select id="NoEmpleadoSelect" class="form-select">
          <option value="">— Todos —</option>
          @foreach($empleados as $e)
            @php
              $nombreCompleto = trim(($e->APaterno ? $e->APaterno . ' ' : '') . ($e->AMaterno ? $e->AMaterno . ' ' : '') . $e->Nombre);
              $label = ($e->No_Empleado ?? '') . ' — ' . $nombreCompleto; // visible en el dropdown
            @endphp
            {{-- VALUE = No_Empleado (lo que consume tu SP). Si prefieres Id_Empleado, cambia aquí y en backend. --}}
            <option value="{{ $e->No_Empleado }}">{{ $label }}</option>
          @endforeach
        </select>
        {{-- Hidden que se envía por AJAX como NoEmpleado --}}
        <input type="hidden" name="NoEmpleado" id="NoEmpleado" value="">
      </div>

      <div class="col-md-4">
        <label class="form-label">Artículo</label>
        <select id="ArticuloSelect" class="form-select">
          <option value="">— Todos —</option>
          @foreach($productos as $p)
            <option value="{{ $p->Txt_Descripcion }}">{{ $p->Txt_Descripcion }}</option>
          @endforeach
        </select>
        {{-- Hidden que se envía por AJAX --}}
        <input type="hidden" name="Articulo" id="Articulo" value="">
      </div>

      <div class="col-md-3 d-flex align-items-end gap-2">
        <button class="btn btn-primary flex-grow-1" type="submit">Filtrar</button>
        <button class="btn btn-outline-secondary" type="button" id="btnTodos" title="Limpiar filtros"><i
            class="fas fa-sync-alt"></i></button>
        <button class="btn btn-success" type="button" id="btnExport" title="Exportar Excel"><i
            class="fas fa-file-excel"></i></button>
      </div>

      <div class="col-12 d-flex gap-3">
        <div class="badge-chip">
          <span
            style="display:inline-block;width:10px;height:10px;background:var(--urv-azul);border-radius:2px;margin-right:6px"></span>
          Consumido
        </div>
        <div class="badge-chip">
          <span
            style="display:inline-block;width:10px;height:10px;background:var(--urv-verde);border-radius:2px;margin-right:6px"></span>
          Disponible
        </div>
      </div>
    </form>
  </div>

  <div class="card p-3">
    <table id="tblConsumos" class="table table-striped table-hover w-100">
      <thead>
        <tr>
          <th>No_Empleado</th>
          <th>Nombre</th>
          <th>Imagen</th>
          <th>Artículo</th>
          <th class="text-end">Frecuencia (dias)</th>
          <th class="text-end">Permitida</th>
          <th class="text-end">Consumido</th>
          <th class="text-end">Disponible</th>
          <th>Estado</th> <!-- NUEVO -->
          <th>Progreso</th> <!-- Unificado -->
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
  </div>

  <div class="card ">
    <div class="container py-3">
      <!-- Resumen + filtros -->
      <div class="row g-3 mb-3">
        <div class="col-md-3">
          <div class="kpi">
            <div class="t">Total permitido</div>
            <div class="v" id="kpiPermitida">0</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi">
            <div class="t">Total consumido</div>
            <div class="v" id="kpiConsumida">0</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi">
            <div class="t">Total disponible</div>
            <div class="v" id="kpiDisponible">0</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi">
            <div class="t">% Uso global</div>
            <div class="v" id="kpiPct">0%</div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="todos">Todos</button>
        <button type="button" class="btn btn-sm btn-outline-warning" data-filter="poragotar">Por agotar</button>
        <button type="button" class="btn btn-sm btn-outline-danger" data-filter="agotados">Agotados / Excedidos</button>
      </div>

      <!-- (Opcional) mini gráfica -->
      <div class="chart-h-110 mb-3">
        <canvas id="consumosChart"></canvas>
      </div>
    </div>





@endsection


  @section('right-sidebar')
  @stop

  @section('css')
  @stop

  @section('js')
  <!-- jQuery: debe ir ANTES de jQuery UI, DataTables y de tu script -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Fallback local opcional si el CDN falla (descarga jquery-3.6.0.min.js a /public/vendor/) -->
  <script>window.jQuery || document.write('<script src="/vendor/jquery-3.6.0.min.js"><\/script>')</script>

  <!-- jQuery UI -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>

  <!-- DataTables 1.11.3 (jQuery plugin) + Bootstrap 4 -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

  <!-- Chart.js (opcional) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Choices.js (para el select buscable; NO usamos Select2 aquí) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <!-- TU SCRIPT: debe ir DESPUÉS de todo lo anterior -->
  <script>
    $(function () {
      // ===== Choices (buscar número o nombre) =====
      const $sel = $('#NoEmpleadoSelect');
      const empChoices = new Choices($sel[0], {
        searchEnabled: true,
        searchFields: ['label', 'value'],
        searchPlaceholderValue: 'Escribe número o nombre…',
        placeholder: true, placeholderValue: '— Todos —',
        shouldSort: true, itemSelectText: '', removeItemButton: true
      });
      function syncEmpleado() { $('#NoEmpleado').val($sel.val() || ''); }
      syncEmpleado(); $sel.on('change', syncEmpleado);

      // ===== Auto-select from URL =====
      const urlParams = new URLSearchParams(window.location.search);
      const empId = urlParams.get('employee_id');
      if (empId) {
        empChoices.setChoiceByValue(empId);
        // Trigger change to update hidden input and reload table if needed (though table init will pick it up if we set it before)
        $sel.val(empId).trigger('change');
      }

      // ===== Choices (buscar Artículo) =====
      const $selArt = $('#ArticuloSelect');
      const artChoices = new Choices($selArt[0], {
        searchEnabled: true,
        searchPlaceholderValue: 'Buscar artículo…',
        placeholder: true, placeholderValue: '— Todos —',
        shouldSort: true, itemSelectText: '', removeItemButton: true
      });
      function syncArticulo() { $('#Articulo').val($selArt.val() || ''); }
      syncArticulo(); $selArt.on('change', syncArticulo);

      // ===== Helpers =====
      function clampPct(n) { return Math.max(0, Math.min(100, Math.round(n))); }
      function safeNum(x) { const n = Number(x || 0); return isFinite(n) ? n : 0; }
      function usoPct(consumida, base) { base = safeNum(base); if (!base) return 0; return clampPct(100 * safeNum(consumida) / base); }

      // Estado por fila
      function estadoInfo(row) {
        const pmt = safeNum(row.Cantidad_Permitida);
        const con = safeNum(row.Cantidad_Consumida);
        const disp = safeNum(row.Disponible);
        const base = pmt || (con + disp) || 1;
        const p = usoPct(con, base);

        if (con > pmt && pmt > 0) return { t: 'Excedido', cls: 'bad' };
        if (p >= 100) return { t: 'Agotado', cls: 'bad' };
        if (p >= 80) return { t: 'Por agotar', cls: 'warn' };
        return { t: 'OK', cls: 'ok' };
      }

      // Barra unificada (un solo track)
      function renderUniBar(row) {
        const pmt = safeNum(row.Cantidad_Permitida);
        const con = safeNum(row.Cantidad_Consumida);
        const disp = safeNum(row.Disponible);
        const base = pmt || (con + disp) || 1;

        const pCons = usoPct(con, base);     // % consumido del total base
        const pDisp = usoPct(disp, base);    // % disponible del total base
        const exceso = con > pmt && pmt > 0 ? clampPct(100 * (con - pmt) / base) : 0;

        // Segmentos: consumido (azul), disponible (verde). Si hay exceso, cubre encima en rojo desde el final de consumido.
        const segConsumido = `<span class="seg consumido" style="width:${pCons}%;"></span>`;
        const segDisponible = `<span class="seg disponible" style="left:${pCons}%;width:${pDisp}%;"></span>`;
        const segExcedido = exceso ? `<span class="seg excedido" style="left:${pCons}%;width:${exceso}%;"></span>` : '';

        const tip = `Permitida: ${pmt} | Consumido: ${con} | Disponible: ${disp}`;
        return `
      <div title="${tip}">
        <div class="uni-bar">${segConsumido}${segDisponible}${segExcedido}</div>
        <div class="uni-meta mt-1">${con} / ${pmt || (con + disp)} · ${pCons}% usado</div>
      </div>
    `;
      }

      function renderEstado(row) {
        const s = estadoInfo(row);
        return `<span class="chip ${s.cls}" data-status="${s.t}">${s.t}</span>`;
      }

      function renderImagen(data, type, row) {
        if (!data) return '<span class="text-muted" style="font-size:10px;">Sin imagen</span>';
        return `<img src="/Images/Catalogo/${data}.jpg" alt="${data}" style="width: 50px; height: 50px; object-fit: contain;" onerror="this.onerror=null;this.src='/Images/product.png';">`;
      }

      // ===== DataTable =====
      const table = $('#tblConsumos').DataTable({
        processing: true, serverSide: false, paging: true, searching: true, lengthChange: true, pageLength: 10,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json' },
        ajax: {
          url: '/cli/reporte/consultaconsumos/data', type: 'POST',
          data: function (d) {
            d._token = $('input[name="_token"]').val();
            d.NoEmpleado = $('#NoEmpleado').val();
            d.Articulo = $('#Articulo').val();
          },
          error: function (xhr) {
            console.error('Error AJAX:', xhr.responseText || xhr.statusText);
            alert('No se pudo cargar la información. Revisa consola/log.');
          }
        },
        columns: [
          { data: 'No_Empleado' },
          { data: 'Nombre' },
          { data: 'Codigo_Urvina', render: renderImagen, orderable: false, searchable: false },
          { data: 'Articulo' },
          { data: 'Frecuencia', className: 'text-right' },
          { data: 'Cantidad_Permitida', className: 'text-right' },
          { data: 'Cantidad_Consumida', className: 'text-right' },
          { data: 'Disponible', className: 'text-right' },
          { data: null, render: renderEstado, orderable: false, searchable: true }, // ESTADO
          { data: null, render: renderUniBar, orderable: false, searchable: false } // PROGRESO unificado
        ],
        createdRow: function (row, data) {
          const s = estadoInfo(data);
          if (s.cls === 'bad') $(row).addClass('alerta');
        }
      });

      // ===== Filtros rápidos por estado =====
      const estadoColIdx = 8; // índice de la columna "Estado" según "columns" arriba
      $('[data-filter]').on('click', function () {
        const m = $(this).data('filter');
        if (m === 'todos') {
          table.column(estadoColIdx).search('').draw();
        } else if (m === 'poragotar') {
          table.column(estadoColIdx).search('Por agotar', true, false).draw();
        } else if (m === 'agotados') {
          table.column(estadoColIdx).search('(Agotado|Excedido)', true, false).draw();
        }
      });

      // ===== KPIs + Gráfica =====
      let chart;
      function actualizarResumen(rows) {
        let P = 0, C = 0, D = 0;
        rows.forEach(r => { P += safeNum(r.Cantidad_Permitida); C += safeNum(r.Cantidad_Consumida); D += safeNum(r.Disponible); });
        const base = P || (C + D);
        const pct = base ? Math.round((C / base) * 100) : 0;

        $('#kpiPermitida').text(P);
        $('#kpiConsumida').text(C);
        $('#kpiDisponible').text(D);
        $('#kpiPct').text(pct + '%');

        const ctx = document.getElementById('consumosChart').getContext('2d');
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['Consumida', 'Disponible'],
            datasets: [{ data: [C, D] }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,   // <-- clave: respeta la altura del contenedor
            plugins: {
              legend: { position: 'bottom', labels: { boxWidth: 10 } }
            },
            layout: { padding: { top: 4, bottom: 4 } }
          }
        });

      }

      // Al cargar datos
      $('#tblConsumos').on('xhr.dt', function (_e, _settings, json) {
        if (json && Array.isArray(json.data)) actualizarResumen(json.data);
      });

      // Buscar / Todos
      $('#filtro').on('submit', function (e) { e.preventDefault(); table.ajax.reload(); });
      $('#btnTodos').on('click', function () {
        empChoices.removeActiveItems(); $sel.val('').trigger('change');
        artChoices.removeActiveItems(); $selArt.val('').trigger('change');
        $('#NoEmpleado').val('');
        $('#Articulo').val('');
        table.ajax.reload();
      });

      // Exportar Excel
      $('#btnExport').on('click', function () {
        const noEmp = $('#NoEmpleado').val() || '';
        const art = $('#Articulo').val() || '';

        // Construir URL basándonos en el path actual para mantener el idioma dinámico
        // Actual: /{lang}/reporte/consultaconsumos
        // Destino: /{lang}/export/consultaconsumos
        const currentPath = window.location.pathname;
        const newPath = currentPath.replace('/reporte/consultaconsumos', '/export/consultaconsumos');
        const url = window.location.origin + newPath + "?NoEmpleado=" + encodeURIComponent(noEmp) + "&Articulo=" + encodeURIComponent(art);

        window.location.href = url;
      });
    });
  </script>

  <script>
    function goBack() {
      window.history.back();
    }
  </script>
  @stop