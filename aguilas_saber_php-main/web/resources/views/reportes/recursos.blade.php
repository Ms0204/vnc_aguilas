<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Recursos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Reporte de Recursos</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recursos as $recurso)
            <tr>
                <td>{{ $recurso->nombre }}</td>
                <td>{{ $recurso->descripcion ?? '—' }}</td>
                <td>{{ ucfirst($recurso->estado) }}</td>
                <td>{{ $recurso->cantidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>