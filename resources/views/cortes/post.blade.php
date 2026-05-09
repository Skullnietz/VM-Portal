@extends('adminlte::page')

@section('title', 'Corte Post-Resurtimiento')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Corte Post-Resurtimiento
            </h4>
        </div>
        <div class="col text-right">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container">
    {{-- Info del corte --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Máquina</span>
                    <span class="info-box-number">{{ $corte->Maquina }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-industry"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Planta</span>
                    <span class="info-box-number">{{ $corte->Planta }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Fecha del Corte</span>
                    <span class="info-box-number">{{ \Carbon\Carbon::parse($corte->Fecha_Corte)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen comparativo agregado --}}
    @if($resumen->count() > 0)
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="fas fa-balance-scale"></i> Resumen: Planeado vs Real</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Artículo</th>
                        <th>Código</th>
                        <th>Talla</th>
                        <th class="text-center">Planeado</th>
                        <th class="text-center">Real</th>
                        <th class="text-center">Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resumen as $item)
                    @php
                        $diff = $item->Total_Rellenado - $item->Total_Planeado;
                        $diffClass = $diff == 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-warning');
                    @endphp
                    <tr>
                        <td>{{ $item->Txt_Descripcion }}</td>
                        <td>{{ $item->Txt_Codigo }}</td>
                        <td>{{ $item->Talla ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->Total_Planeado }}</td>
                        <td class="text-center font-weight-bold">{{ $item->Total_Rellenado }}</td>
                        <td class="text-center font-weight-bold {{ $diffClass }}">
                            {{ $diff >= 0 ? '+' . $diff : $diff }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Detalle por selección --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detalle por Selección: Planeado vs Real</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Selección</th>
                        <th>Artículo</th>
                        <th>Talla</th>
                        <th class="text-center">Stock Antes</th>
                        <th class="text-center">Planeado</th>
                        <th class="text-center">Stock Después</th>
                        <th class="text-center">Rellenado Real</th>
                        <th class="text-center">Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $item)
                    @php
                        $diff = ($item->Cantidad_Rellenada ?? 0) - ($item->Cantidad_Necesaria ?? 0);
                        $rowClass = '';
                        if ($diff < 0) $rowClass = 'table-danger';
                        elseif ($diff == 0 && $item->Cantidad_Necesaria > 0) $rowClass = 'table-success';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="font-weight-bold">{{ $item->Seleccion }}</td>
                        <td>{{ $item->Txt_Descripcion ?? 'N/A' }}</td>
                        <td>{{ $item->Talla ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->Stock_Actual }}</td>
                        <td class="text-center">{{ $item->Cantidad_Necesaria }}</td>
                        <td class="text-center font-weight-bold">{{ $item->Stock_Post ?? '-' }}</td>
                        <td class="text-center font-weight-bold">{{ $item->Cantidad_Rellenada ?? 0 }}</td>
                        <td class="text-center font-weight-bold {{ $diff < 0 ? 'text-danger' : ($diff > 0 ? 'text-warning' : 'text-success') }}">
                            {{ $diff >= 0 ? '+' . $diff : $diff }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    @media print {
        .no-print, .btn { display: none !important; }
        .card, .card-body { border: none !important; box-shadow: none !important; }
        .info-box { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@stop

@section('js')
<script>
    function goBack() { window.history.back(); }
</script>
@stop
