<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 26px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        h1 { margin:0 0 6px; font-size: 18px; }
        .muted { color:#555; }
        .box { border:1px solid #d0d7de; border-radius:8px; padding:10px; margin-bottom:10px; }
        ul { margin:0; padding-left:16px; }
    </style>
</head>
<body>
    <h1>Recetario #{{ $prescription->id }}</h1>
    <p class="muted">Paciente: {{ trim(optional($prescription->historiaClinica?->paciente)->nombres . ' ' . optional($prescription->historiaClinica?->paciente)->apellidos) }}</p>
    <div class="box">
        <strong>Profesional:</strong> {{ optional($prescription->professional)->name ?: 'N/D' }}<br>
        <strong>Fecha:</strong> {{ $prescription->created_at?->format('d/m/Y') }}
    </div>
    <div class="box">
        <strong>Medicamentos / productos</strong>
        <ul>
            @foreach($prescription->items as $item)
                <li>
                    <strong>{{ $item->is_manual ? $item->manual_name : optional($item->product)->name }}</strong>
                    @if($item->is_manual)
                        <span class="muted">(no facturable)</span>
                    @endif
                    — Cantidad: {{ $item->qty_requested }}<br>
                    <span class="muted">Dosis: {{ $item->dose ?: 'N/D' }} | Freq: {{ $item->frequency ?: 'N/D' }} | Días: {{ $item->duration_days ?: 'N/D' }}</span><br>
                    <span>{{ $item->instructions }}</span>
                </li>
            @endforeach
        </ul>
    </div>
    <p class="muted">Formato media carta listo para impresión. Los medicamentos ingresados manualmente no se envían a facturación.</p>
</body>
</html>
