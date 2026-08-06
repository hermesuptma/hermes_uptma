<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; }
        .carnet {
            width: 300px;
            height: 180px;
            border: 2px solid #1e3a8a;
            border-radius: 10px;
            padding: 12px;
            margin: 10px;
            display: inline-block;
            page-break-inside: avoid;
        }
        .titulo { font-size: 10px; color: #1e3a8a; font-weight: bold; text-transform: uppercase; }
        .nombre { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .dato { font-size: 11px; color: #444; margin-top: 2px; }
        .qr { text-align: center; margin-top: 8px; }
    </style>
</head>
<body>
    @foreach ($estudiantes as $estudiante)
        <div class="carnet">
            <div class="titulo">Hermes UPTMA</div>
            <div class="nombre">{{ $estudiante->nombre }}</div>
            <div class="dato">C.I. {{ $estudiante->cedula }}</div>
            <div class="qr">
                <img src="{{ $qrs[$estudiante->id] }}" width="90" height="90">
            </div>
        </div>
    @endforeach
</body>
</html>