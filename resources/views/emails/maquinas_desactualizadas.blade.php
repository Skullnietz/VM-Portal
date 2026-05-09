@component('mail::message')
# Reporte no enviado

Estimado usuario,

El reporte correspondiente al periodo **{{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}** no fue enviado porque se detectaron **máquinas sin sincronización actualizada**.

A continuación se detallan las máquinas que requieren atención:

@component('mail::table')
| ID Máquina | Nombre           | Estatus | Última sincronización |
|------------|------------------|---------|------------------------|
@foreach ($maquinas as $m)
| {{ $m->Id_Maquina }} | {{ $m->Txt_Nombre ?? '-' }} | {{ $m->Txt_Estatus ?? '-' }} | 
{{ $m->Ultima_Sincronizacion ? \Carbon\Carbon::parse($m->Ultima_Sincronizacion)->format('d/m/Y H:i') : 'Sin sincronización' }} |
@endforeach
@endcomponent

Por favor, revisa la conectividad de los dispositivos para que los reportes puedan generarse correctamente.

Gracias por tu atención.

@endcomponent
