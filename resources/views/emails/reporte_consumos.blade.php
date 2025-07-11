@component('mail::message')
# 📊 Reporte de Consumos

Este correo contiene el reporte de consumos correspondiente al periodo:

**{{ $fechaInicio->format('d/m/Y') }} al {{ $fechaFin->format('d/m/Y') }}**

Se adjunta el archivo **Excel** con la información solicitada.

Gracias,  
Portal VM
@endcomponent