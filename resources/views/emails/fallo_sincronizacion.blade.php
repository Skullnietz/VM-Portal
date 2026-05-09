@component('mail::message')
# ❌ Alerta de sincronización

Se ha detectado que las siguientes máquinas de la planta **{{ $nombrePlanta }}** no están sincronizadas correctamente:

@component('mail::table')
| ID de Máquina | Nombre de Máquina | Estatus     |
|---------------|-------------------|-------------|
@foreach ($maquinas as $m)
| {{ $m->Id_Maquina }} | {{ $m->Txt_Nombre }} | {{ $m->Txt_Estatus }} |
@endforeach
@endcomponent

Por favor, revise el estado de estas máquinas en el sistema.

Gracias,  
Portal USI VM
@endcomponent