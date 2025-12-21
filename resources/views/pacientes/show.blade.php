@extends('layouts.vertical', ['subtitle' => 'Pacientes'])

@section('content')
    @include('layouts.partials/page-title', ['title' => 'Pacientes', 'subtitle' => 'Perfil'])

    @php
        $iniciales = collect([$paciente->nombres, $paciente->apellidos])
            ->filter()
            ->map(fn($parte) => mb_substr($parte, 0, 1))
            ->join('');
    @endphp

    <div class="card patient-hero border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white bg-opacity-25 text-white d-flex align-items-center justify-content-center avatar-xl"
                    style="font-weight: 800; font-size: 1.75rem;">
                    {{ $iniciales ?: 'P' }}
                </div>
                <div>
                    <p class="mb-1 text-white-50 small">Paciente #{{ $paciente->id }}</p>
                    <h3 class="mb-1">{{ $paciente->nombres }} {{ $paciente->apellidos }}</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge stat-pill">{{ $paciente->tipo_documento ?: 'Documento' }} {{ $paciente->numero_documento ?: 'no asignado' }}</span>
                        <span class="badge stat-pill">{{ $paciente->sexo ? ucfirst($paciente->sexo) : 'Sexo no especificado' }}</span>
                        <span class="badge stat-pill">{{ $paciente->ciudad ?: 'Ciudad sin registrar' }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3">
                <div class="text-center">
                    <p class="mb-1 text-white-50 small">Edad</p>
                    <h5 class="mb-0">{{ $edad ? $edad . ' años' : 'Sin dato' }}</h5>
                </div>
                <a href="{{ url('/historias-clinicas/create') }}?paciente_id={{ $paciente->id }}" class="btn btn-gradient px-4 py-2">
                    Crear consulta
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card surface-card shadow-sm h-100 sticky-top" style="top: 1.25rem;">
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="section-title mb-1">Datos básicos</h6>
                        <p class="text-muted small mb-0">Información de contacto y residencia.</p>
                    </div>

                    <div class="d-grid gap-3 mb-3">
                        <div class="info-chip d-flex align-items-center">
                            <i class="ri-whatsapp-line me-2"></i>
                            <span>{{ $paciente->whatsapp }}</span>
                        </div>
                        <div class="info-chip d-flex align-items-center">
                            <i class="ri-mail-line me-2"></i>
                            <span>{{ $paciente->email ?: 'Correo pendiente' }}</span>
                        </div>
                        <div class="info-chip d-flex align-items-start">
                            <i class="ri-map-pin-line me-2 mt-1"></i>
                            <div>
                                <div>{{ $paciente->direccion ?: 'Dirección pendiente' }}</div>
                                <small class="text-muted">{{ $paciente->ciudad ?: 'Ciudad no registrada' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-white border mb-3">
                        <p class="text-muted small mb-1">Contacto alterno</p>
                        <h6 class="mb-1">{{ $paciente->acompanante ?: 'Acompañante no registrado' }}</h6>
                        <p class="text-muted mb-0">{{ $paciente->acompanante_contacto ?: 'Número de contacto no registrado' }}</p>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-gradient">
                            Editar perfil
                        </a>
                        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">Volver a pacientes</a>
                        <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}"
                            onsubmit="return confirm('¿Deseas eliminar este paciente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">Eliminar paciente</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 surface-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted mb-1">Historia clínica</p>
                            <h5 class="mb-0 section-title">{{ $historiaReciente ? ucfirst($historiaReciente->estado) : 'Sin historia registrada' }}</h5>
                        </div>
                        @if ($historiaReciente)
                            <span class="badge bg-primary bg-opacity-75">Actualizada {{ optional($historiaReciente->updated_at)->diffForHumans() }}</span>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 metric-tile h-100">
                                <p class="text-muted small mb-1">Tensión arterial</p>
                                <h5 class="mb-0">{{ $historiaReciente?->tension_arterial ?? 'Sin dato' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 metric-tile h-100">
                                <p class="text-muted small mb-1">Frecuencia cardiaca</p>
                                <h5 class="mb-0">{{ $historiaReciente?->frecuencia_cardiaca ?? 'Sin dato' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 metric-tile h-100">
                                <p class="text-muted small mb-1">Saturación O₂</p>
                                <h5 class="mb-0">{{ $historiaReciente?->saturacion_oxigeno ?? 'Sin dato' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 metric-tile h-100">
                                <p class="text-muted small mb-1">Frecuencia respiratoria</p>
                                <h5 class="mb-0">{{ $historiaReciente?->frecuencia_respiratoria ?? 'Sin dato' }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(117, 96, 168, 0.08);">
                                <p class="text-muted small mb-1">Motivo de consulta</p>
                                <h6 class="mb-2">{{ $historiaReciente?->motivo_consulta ?? 'Aún no se ha documentado el motivo de consulta.' }}</h6>
                                <p class="text-muted small mb-1">Enfermedad actual</p>
                                <p class="mb-0">{{ $historiaReciente?->enfermedad_actual ?? 'Sin descripción clínica reciente.'}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(79, 132, 196, 0.1);">
                                <p class="text-muted small mb-1">Plan</p>
                                <p class="mb-2">{{ $historiaReciente?->plan_medicamentos ?? 'Agrega el plan de medicamentos y procedimientos.' }}</p>
                                <p class="text-muted small mb-1">Procedimientos</p>
                                <p class="mb-0">{{ $historiaReciente?->plan_procedimientos ?? 'Sin procedimientos programados.'}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100 surface-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0 section-title">Diagnósticos</h5>
                                <span class="badge bg-secondary bg-opacity-75">{{ $historiaReciente?->diagnosticos->count() ?? 0 }} activos</span>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse ($historiaReciente?->diagnosticos ?? [] as $diagnostico)
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $diagnostico->codigo }} - {{ $diagnostico->descripcion }}</h6>
                                                <small class="text-muted">{{ $diagnostico->confirmado ? 'Confirmado' : 'Pendiente de confirmación' }}</small>
                                            </div>
                                            <span class="badge {{ $diagnostico->confirmado ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $diagnostico->confirmado ? 'Confirmado' : 'Sugerido' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Aún no hay diagnósticos registrados.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100 surface-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0 section-title">Paraclínicos</h5>
                                <span class="badge bg-secondary bg-opacity-75">{{ $historiaReciente?->paraclinicos->count() ?? 0 }} registrados</span>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse ($historiaReciente?->paraclinicos ?? [] as $paraclinico)
                                    <div class="list-group-item px-0 border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $paraclinico->nombre }}</h6>
                                                <small class="text-muted">{{ $paraclinico->resultado ?: 'Resultado pendiente' }}</small>
                                            </div>
                                            <i class="ri-flask-line text-primary"></i>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Incluye laboratorios, imágenes o estudios solicitados.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4 surface-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 section-title">Sugerencias para completar el perfil</h5>
                        <i class="ri-lightbulb-flash-line text-warning"></i>
                    </div>
                    @if (count($sugerencias))
                        <ul class="list-unstyled mb-0">
                            @foreach ($sugerencias as $sugerencia)
                                <li class="d-flex align-items-start mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    <span>{{ $sugerencia }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">El perfil del paciente está completo y al día.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
