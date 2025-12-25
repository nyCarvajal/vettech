<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color:#111; }
        h1,h2,h3 { margin: 0; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .box { border:1px solid #ccc; border-radius:8px; padding:10px; margin-bottom:10px; }
        .muted { color:#555; }
        .grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:8px; }
        .tag { background:#f1f5f9; padding:6px 8px; border-radius:6px; }
        .list-item { padding:6px 0; border-bottom:1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Historia clínica #{{ $historiaClinica->id }}</h1>
            <p class="muted">Paciente: {{ trim(($historiaClinica->paciente->nombres ?? '') . ' ' . ($historiaClinica->paciente->apellidos ?? '')) }}</p>
        </div>
        <div class="muted">{{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="box">
        <h3>Motivo y antecedentes</h3>
        <p><strong>Motivo de consulta:</strong> {{ $historiaClinica->motivo_consulta ?: 'Sin registrar' }}</p>
        <p><strong>Enfermedad actual:</strong> {{ $historiaClinica->enfermedad_actual ?: 'Sin registrar' }}</p>
        <p><strong>Antecedentes farmacológicos:</strong> {{ $historiaClinica->antecedentes_farmacologicos ?: 'N/D' }}</p>
        <p><strong>Antecedentes patológicos:</strong> {{ $historiaClinica->antecedentes_patologicos ?: 'N/D' }}</p>
    </div>

    <div class="box">
        <h3>Tutor</h3>
        <p class="muted">{{ optional($historiaClinica->paciente?->owner)->name }}</p>
        <div class="grid">
            <div class="tag">Tel: {{ optional($historiaClinica->paciente?->owner)->phone }}</div>
            <div class="tag">WhatsApp: {{ optional($historiaClinica->paciente?->owner)->whatsapp }}</div>
            <div class="tag">Correo: {{ optional($historiaClinica->paciente?->owner)->email }}</div>
        </div>
    </div>

    <div class="box">
        <h3>Diagnósticos</h3>
        @forelse($historiaClinica->diagnosticos as $diag)
            <div class="list-item">
                <strong>{{ $diag->descripcion }}</strong> — {{ $diag->codigo ?: 'Sin código' }}
            </div>
        @empty
            <p class="muted">Sin diagnósticos.</p>
        @endforelse
    </div>

    <div class="box">
        <h3>Paraclínicos</h3>
        @forelse($historiaClinica->paraclinicos as $item)
            <div class="list-item">
                <strong>{{ $item->nombre }}</strong>
                <div class="muted">{{ $item->resultado ?: 'Pendiente' }}</div>
            </div>
        @empty
            <p class="muted">Sin paraclínicos agregados.</p>
        @endforelse
    </div>

    <div class="box">
        <h3>Plan</h3>
        <p><strong>Análisis:</strong> {{ $historiaClinica->analisis ?: 'N/D' }}</p>
        <p><strong>Procedimientos:</strong> {{ $historiaClinica->plan_procedimientos ?: 'N/D' }}</p>
        <p><strong>Medicamentos:</strong> {{ $historiaClinica->plan_medicamentos ?: 'N/D' }}</p>
    </div>
</body>
</html>
