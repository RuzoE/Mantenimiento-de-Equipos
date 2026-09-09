<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: "DejaVu Sans", sans-serif; }
        body { font-size: 11px; color: #222; margin: 0; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        h2 { font-size: 12px; margin: 14px 0 4px; border-bottom: 1px solid #ddd; padding-bottom: 2px; }
        .muted { color: #666; font-size: 10px; margin: 0 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #eef2f7; }
        .resumen { border: none; margin-bottom: 4px; }
        .resumen td { border: none; padding: 1px 0; }
        .resumen td.n { text-align: right; width: 60px; color: #444; }
        .col2 { width: 48%; display: inline-block; vertical-align: top; }
    </style>
</head>
<body>
    <h1>Reporte de mantenimientos</h1>
    <p class="muted">
        Institución Educativa Policarpa Salavarrieta &middot;
        Generado el {{ $generado->format('d/m/Y H:i') }} &middot;
        Total: {{ $reporte['total'] }} &middot;
        Programaciones próximas: {{ $reporte['programacion']['proximas'] }} &middot;
        vencidas: {{ $reporte['programacion']['vencidas'] }}
    </p>

    <h2>Resumen</h2>
    @foreach ($reporte['agrupaciones'] as $titulo => $filas)
        <div class="col2">
            <strong>{{ $titulo }}</strong>
            <table class="resumen">
                @forelse ($filas as $f)
                    <tr><td>{{ $f['label'] }}</td><td class="n">{{ $f['total'] }}</td></tr>
                @empty
                    <tr><td>Sin datos</td></tr>
                @endforelse
            </table>
        </div>
    @endforeach

    <h2>Detalle</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th><th>Equipo</th><th>Tipo</th><th>Técnico</th>
                <th>Estado antes</th><th>Estado después</th><th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reporte['filas'] as $m)
                <tr>
                    <td>{{ $m->fecha->format('d/m/Y') }}</td>
                    <td>{{ $m->equipo->codigo_interno }}</td>
                    <td>{{ $m->tipo->label() }}</td>
                    <td>{{ $m->responsable?->nombre ?? 'Sin asignar' }}</td>
                    <td>{{ $m->estado_antes?->label() }}</td>
                    <td>{{ $m->estado_despues?->label() }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($m->descripcion, 90) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
