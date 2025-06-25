
<h2>⚠️ Alerta de Máquina Desactivada</h2>
<p><strong>Máquina:</strong> {{ $maquina->Txt_Nombre }} (ID: {{ $maquina->Id_Maquina }})</p>
<p><strong>Planta:</strong> {{ $maquina->Id_Planta }}</p>
<p><strong>Última comunicación:</strong> {{ $maquina->Fecha_Reg }}</p>
<p><strong>Estado actual:</strong> {{ $maquina->Txt_Estatus }}</p>
<p style="color:red;"><strong>Descripción:</strong> No hay comunicación con el dispositivo.</p>


