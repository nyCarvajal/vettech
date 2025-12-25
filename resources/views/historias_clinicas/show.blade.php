@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0">Historia clínica #{{ $historia->id }}</h1>
            <p class="text-muted mb-0">Paciente: {{ trim(($historia->paciente->nombres ?? '') . ' ' . ($historia->paciente->apellidos ?? '')) ?: 'Sin paciente' }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('historias-clinicas.edit', $historia) }}" class="btn btn-outline-secondary">Editar</a>
            <a href="{{ route('historias-clinicas.recetarios.create', $historia) }}" class="btn btn-primary">Agregar recetario</a>
            <a href="{{ route('historias-clinicas.remisiones.create', $historia) }}" class="btn btn-info text-white">Nueva remisión</a>
            <a href="{{ route('historias-clinicas.pdf', $historia) }}" class="btn btn-success">Imprimir PDF</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">Motivo y antecedentes</div>
                <div class="card-body">
                    <p><strong>Motivo de consulta:</strong> {{ $historia->motivo_consulta ?: 'Sin registrar' }}</p>
                    <p><strong>Antecedentes / Enfermedad actual:</strong> {{ $historia->enfermedad_actual ?: 'Sin registrar' }}</p>
                    <p><strong>Antecedentes farmacológicos:</strong> {{ $historia->antecedentes_farmacologicos ?: 'N/D' }}</p>
                    <p><strong>Antecedentes patológicos:</strong> {{ $historia->antecedentes_patologicos ?: 'N/D' }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Paraclínicos solicitados</div>
                <div class="card-body">
                    @forelse($historia->paraclinicos as $paraclinico)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <strong>{{ $paraclinico->nombre }}</strong>
                                <div class="text-muted small">{{ $paraclinico->resultado ?: 'Pendiente' }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay paraclínicos agregados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Diagnósticos</div>
                <div class="card-body">
                    @forelse($historia->diagnosticos as $diagnostico)
                        <div class="border-bottom py-2">
                            <strong>{{ $diagnostico->descripcion }}</strong>
                            <div class="text-muted small">{{ $diagnostico->codigo ?: 'Sin código' }} · {{ $diagnostico->confirmado ? 'Confirmado' : 'Presuntivo' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay diagnósticos registrados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Plan y análisis</div>
                <div class="card-body">
                    <p><strong>Análisis:</strong></p>
                    <p class="text-muted">{{ $historia->analisis ?: 'Sin registrar' }}</p>
                    <p><strong>Plan procedimientos:</strong></p>
                    <p class="text-muted">{{ $historia->plan_procedimientos ?: 'Sin registrar' }}</p>
                    <p><strong>Plan medicamentos:</strong></p>
                    <p class="text-muted">{{ $historia->plan_medicamentos ?: 'Sin registrar' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">Tutor</div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ optional($historia->paciente?->owner)->name ?? 'Sin tutor' }}</strong></p>
                    <p class="text-muted mb-1">Tel: {{ optional($historia->paciente?->owner)->phone ?: 'N/D' }}</p>
                    <p class="text-muted mb-1">WhatsApp: {{ optional($historia->paciente?->owner)->whatsapp ?: 'N/D' }}</p>
                    <p class="text-muted mb-0">Correo: {{ optional($historia->paciente?->owner)->email ?: 'N/D' }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recetarios</span>
                </div>
                <div class="card-body">
                    @forelse($prescriptions as $prescription)
                        <div class="border-bottom py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Recetario #{{ $prescription->id }}</strong>
                                    <div class="text-muted small">{{ optional($prescription->professional)->name }}</div>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-primary" href="{{ route('historias-clinicas.recetarios.print', $prescription) }}">PDF</a>
                                    <form method="post" action="{{ route('historias-clinicas.recetarios.facturar', $prescription) }}">
                                        @csrf
                                        <button class="btn btn-outline-success" type="submit">Facturar</button>
                                    </form>
                                </div>
                            </div>
                            <ul class="mb-0 small text-muted">
                                @foreach($prescription->items as $item)
                                    <li>{{ optional($item->product)->name }} ({{ $item->qty_requested }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Sin recetarios aún.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">Remisiones de exámenes</div>
                <div class="card-body">
                    @forelse($referrals as $referral)
                        <div class="border-bottom py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Remisión #{{ $referral->id }}</strong>
                                <div class="text-muted small">{{ $referral->created_at?->format('d/m/Y') }}</div>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('historias-clinicas.remisiones.print', $referral) }}">PDF</a>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Sin remisiones registradas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
