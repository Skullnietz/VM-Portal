<!DOCTYPE html>
<html>
<head>
    <title>Prueba de carga CSV</title>
</head>
<body>
    <h2>Sube un archivo CSV</h2>
    <form action="{{ url('/test-upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="csv_file" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
