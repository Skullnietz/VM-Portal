<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alerta de sincronización</title>
</head>
<body>
    <h2>❌ Alerta de sincronización</h2>

    <p>Se ha detectado que las siguientes máquinas de la planta <strong>{{ $nombrePlanta }}</strong> no están sincronizadas correctamente:</p>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID de Máquina</th>
                <th>Nombre de Máquina</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maquinas as $m)
                <tr>
                    <td>{{ $m->Id_Maquina }}</td>
                    <td>{{ $m->Txt_Nombre }}</td>
                    <td>{{ $m->Txt_Estatus }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Por favor, revise el estado de estas máquinas en el sistema.</p>

    <p>Gracias,<br>
    Portal VM</p>
</body>
</html>