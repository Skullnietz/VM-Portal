@extends('adminlte::page')

@section('title', 'Corte Pre-Resurtimiento')

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col text-left">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;Corte Pre-Resurtimiento
            </h4>
        </div>
        <div class="col text-right">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="/corte/pre/{{ $corte->Id_Corte }}/export" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="/{{ app()->getLocale() }}/stock/rellenar/{{ $corte->Id_Maquina }}?corte_pre={{ $corte->Id_Corte }}"
                class="btn btn-warning btn-sm">
                <i class="fas fa-box-open"></i> Ir a Resurtir
            </a>
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

    {{-- Resumen agregado --}}
    @if($resumen->count() > 0)
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="fas fa-clipboard-list"></i> Resumen de Material Necesario</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Artículo</th>
                        <th>Código</th>
                        <th>Talla</th>
                        <th class="text-center">Total Necesario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resumen as $item)
                    <tr>
                        <td>{{ $item->Txt_Descripcion }}</td>
                        <td>{{ $item->Txt_Codigo }}</td>
                        <td>{{ $item->Talla ?? 'N/A' }}</td>
                        <td class="text-center font-weight-bold text-danger">{{ $item->Total_Necesario }}</td>
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
            <h5 class="card-title">Detalle por Selección</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered mb-0" id="detalleTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Selección</th>
                        <th>Artículo</th>
                        <th>Código</th>
                        <th>Talla</th>
                        <th class="text-center">Stock Actual</th>
                        <th class="text-center">Máximo</th>
                        <th class="text-center">Necesario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $seleccion => $items)
                        @foreach($items as $item)
                        <tr class="{{ $item->Cantidad_Necesaria > 0 ? '' : 'table-success' }}">
                            <td class="font-weight-bold">{{ $item->Seleccion }}</td>
                            <td>{{ $item->Txt_Descripcion ?? 'N/A' }}</td>
                            <td>{{ $item->Txt_Codigo ?? 'N/A' }}</td>
                            <td>{{ $item->Talla ?? 'N/A' }}</td>
                            <td class="text-center">{{ $item->Stock_Actual }}</td>
                            <td class="text-center">{{ $item->Cantidad_Max }}</td>
                            <td class="text-center font-weight-bold {{ $item->Cantidad_Necesaria > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $item->Cantidad_Necesaria }}
                            </td>
                        </tr>
                        @endforeach
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
        .no-print, .btn, .info-box-icon, .content-header .col.text-right { display: none !important; }
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
