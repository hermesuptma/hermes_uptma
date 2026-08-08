<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #222;
        }
        h1 {
            font-size: 16px;
            margin-bottom: 4px;
        }
        p.subtitulo {
            color: #555;
            margin-top: 0;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .centro {
            text-align: center;
        }
        .pie {
            margin-top: 20px;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <h1>Hermes UPTMA — Reporte de Asistencia</h1>
    <p class="subtitulo">
        {{ $seccion->materia->nombre }} - {{ $seccion->nombre_seccion }} ({{ $seccion->trayecto->nombre }})
    </p>

    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                <th class="centro">Sesiones</th>
                <th class="centro">Presente</th>
                <th class="centro">Parcial</th>
                <th class="centro">Faltas</th>
                <th class="centro">% Asistencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resumen as $fila)
                <tr>
                    <td>{{ $fila['estudiante']->nombre }}</td>
                    <td class="centro">{{ $fila['total'] }}</td>
                    <td class="centro">{{ $fila['presentes'] }}</td>
                    <td class="centro">{{ $fila['parciales'] }}</td>
                    <td class="centro">{{ $fila['faltas'] }}</td>
                    <td class="centro">{{ $fila['porcentaje'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="pie">Generado el {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>