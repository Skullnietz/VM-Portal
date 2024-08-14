<!-- resources/views/exports/empleados.blade.php -->
<table>
    <thead>
        <tr>
            <th>No_Empleado</th>
            <th>Nip</th>
            <th>Nombre</th>
            <th>APaterno</th>
            <th>AMaterno</th>
            <th>NArea</th>
            <th>Txt_Estatus</th>
            <th>Tipo_Acceso</th>
        </tr>
    </thead>
    <tbody>
        @foreach($empleados as $empleado)
            <tr>
                <td>{{ $empleado->No_Empleado }}</td>
                <td>{{ $empleado->Nip }}</td>
                <td>{{ $empleado->Nombre }}</td>
                <td>{{ $empleado->APaterno }}</td>
                <td>{{ $empleado->AMaterno }}</td>
                <td>{{ DB::table('Cat_Area')->where('Id_Area', $empleado->Id_Area)->value('Txt_Nombre') }}</td>
                <td>{{ $empleado->Txt_Estatus }}</td>
                <td>{{ $empleado->Tipo_Acceso }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
