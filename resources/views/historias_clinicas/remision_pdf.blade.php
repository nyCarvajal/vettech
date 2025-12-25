<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        h1 { font-size: 18px; margin:0 0 6px; }
        .muted { color:#555; }
        .box { border:1px solid #d0d7de; border-radius:8px; padding:10px; margin-bottom:10px; }
    </style>
</head>
<body>
    <h1>Remisión de exámenes #{{ $examReferral->id }}</h1>
    <p class="muted">Paciente: {{ trim(optional($examReferral->historiaClinica?->paciente)->nombres . ' ' . optional($examReferral->historiaClinica?->paciente)->apellidos) }}</p>
    <div class="box">
        <strong>Médico remitente:</strong> {{ $examReferral->doctor_name ?: 'N/D' }}<br>
        <strong>Fecha:</strong> {{ $examReferral->created_at?->format('d/m/Y') }}
    </div>
    <div class="box">
        <strong>Exámenes solicitados</strong>
        <p>{{ $examReferral->tests }}</p>
    </div>
    @if($examReferral->notes)
        <div class="box">
            <strong>Notas adicionales</strong>
            <p>{{ $examReferral->notes }}</p>
        </div>
    @endif
    <p class="muted">Generado automáticamente - formato media carta listo para impresión.</p>
</body>
</html>
