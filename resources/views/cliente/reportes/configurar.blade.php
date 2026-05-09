@extends('adminlte::page')

@section('title', __('Configuración de Alertas'))

@section('content_header')
<div class="container">
    <div class="row">
        <div class="col-md-9 col-9">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded p-1 text-dark bg-light">
                    <i class="fas fa-arrow-left"></i>
                </a>
                &nbsp;&nbsp;Configuración de Alertas
            </h4>
        </div>
    </div>
</div>
@stop

@section('content')
@php
    $frecuenciaActual = $config->Frecuencia ?? null;
    $emailActual = $config->Email ?? '';
@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="{{ route('reportes.guardar_configuracion') }}">
                @csrf

                <div class="card shadow-sm border border-light">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock mr-2"></i>Preferencias de Envío</h5>
                    </div>
                    <div class="card-body bg-light">
                        <center><div class="form-group mt-3">
                        <label for="notificaciones"><i class="fas fa-bell mr-1"></i> Notificaciones por correo</label><br>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="notificaciones" name="notificaciones"
                                {{ isset($config) && $config->Recibir_Notificaciones ? 'checked' : '' }}>
                            <label class="custom-control-label" for="notificaciones">Recibir notificaciones de máquinas desactivadas</label>
                        </div>
                    </div><hr></center>

                    

                    
                        <p class="mb-4">Selecciona cada cuánto deseas recibir el reporte de empleados:</p>

                        <div class="form-group">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="diario" name="frecuencia" value="diario"
                                    class="custom-control-input" {{ $frecuenciaActual === 'diario' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="diario">📅 Diario</label>
                            </div>

                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="semanal" name="frecuencia" value="semanal"
                                    class="custom-control-input" {{ $frecuenciaActual === 'semanal' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="semanal">🗓️ Semanal</label>
                            </div>

                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" id="mensual" name="frecuencia" value="mensual"
                                    class="custom-control-input" {{ $frecuenciaActual === 'mensual' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="mensual">📆 Mensual</label>
                            </div>

                            <div class="form-group mt-4">
                                <label for="email"><i class="fas fa-envelope mr-1"></i>Correo para recibir reportes</label>
                                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $emailActual) }}" required>
                            </div>
                        </div>

                        @if($frecuenciaActual)
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tu configuración actual es: <strong>{{ ucfirst($frecuenciaActual) }}</strong>.
                            </div>
                        @endif
                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Guardar Configuración
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function goBack() {
        window.history.back();
    }
</script>
@stop