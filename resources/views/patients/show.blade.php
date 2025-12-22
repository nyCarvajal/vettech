@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Perfil del paciente</h1>
            <p class="text-muted mb-0">Vista clínica rápida e intuitiva</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-secondary">Editar</a>
            <a href="{{ route('patients.index') }}" class="btn btn-light">Volver</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 1rem;">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body text-center">
                        <img src="{{ $patient->photo_url }}" alt="Foto" class="rounded mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h3 class="h5 mb-1">{{ $patient->display_name ?: 'Paciente sin nombre' }}</h3>
                        <div class="text-muted mb-2">{{ optional($patient->species)->name }} · {{ optional($patient->breed)->name }}</div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            @if($patient->sexo)
                                <span class="badge bg-light text-dark">Sexo: {{ $patient->sexo }}</span>
                            @endif
                            @if($patient->edad)
                                <span class="badge bg-soft-primary text-primary">{{ $patient->edad }}</span>
                            @endif
                            @if($activeStay)
                                <span class="badge bg-danger">Hospitalizado</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-uppercase text-muted mb-0">Tutor</h6>
                            <a href="{{ route('owners.show', $patient->owner) }}" class="small">Ver ficha</a>
                        </div>
                        <div class="fw-semibold">{{ optional($patient->owner)->name }}</div>
                        <div class="text-muted">{{ optional($patient->owner)->address }}</div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if(optional($patient->owner)->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $patient->owner->whatsapp) }}" class="btn btn-success btn-sm">WhatsApp</a>
                            @endif
                            @if(optional($patient->owner)->phone)
                            <a href="tel:{{ $patient->owner->phone }}" class="btn btn-outline-primary btn-sm">Llamar</a>
                            @endif
                            @if(optional($patient->owner)->email)
                            <a href="mailto:{{ $patient->owner->email }}" class="btn btn-outline-secondary btn-sm">Correo</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted">Datos clínicos</h6>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted">Peso actual</span>
                            <span class="fw-semibold">{{ $patient->peso_actual ? $patient->peso_actual . ' kg' : 'N/D' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted">Alergias</span>
                            <span class="fw-semibold text-danger">{{ $patient->alergias ?: 'Sin registrar' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted">Temperamento</span>
                            <span class="fw-semibold">{{ $patient->temperamento ?: 'N/D' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted">Microchip</span>
                            <span class="fw-semibold">{{ $patient->microchip ?: 'N/A' }}</span>
                        </div>
                        <div class="pt-2">
                            <div class="small text-muted">Notas importantes</div>
                            <div class="fw-semibold">{{ $patient->observaciones ?: 'Sin notas adicionales.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Última consulta (clínico)</p>
                        @if($patient->lastEncounter)
                            <h4 class="h5 mb-1">{{ optional($patient->lastEncounter->occurred_at)->format('d M Y') }}</h4>
                            <p class="mb-2">{{ $patient->lastEncounter->motivo ?? 'Sin motivo registrado' }}</p>
                            <div class="text-muted">Diagnóstico: {{ $patient->lastEncounter->diagnostico ?? 'N/D' }}</div>
                            <div class="text-muted">Plan: {{ $patient->lastEncounter->plan ?? 'Sin plan' }}</div>
                            <div class="text-muted">Peso/Temp: {{ $patient->lastEncounter->peso ? $patient->lastEncounter->peso.' kg' : 'N/D' }} / {{ $patient->lastEncounter->temperatura ? $patient->lastEncounter->temperatura.'°C' : 'N/D' }}</div>
                        @else
                            <p class="mb-0 text-muted">Aún no hay consultas registradas.</p>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-2 align-items-end">
                        <a href="#" class="btn btn-outline-primary">Ver historia completa</a>
                        <a href="#" class="btn btn-primary">Crear nueva consulta</a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Timeline clínico</h5>
                            <p class="text-muted mb-0">Eventos de consultas, baños, hospitalización y ventas.</p>
                        </div>
                        <div class="btn-group" role="group">
                            @php
                                $tabs = [
                                    'timeline' => 'Timeline',
                                    'consulta' => 'Consultas',
                                    'banio' => 'Peluquería',
                                    'hospital' => 'Hospitalización',
                                    'dispensacion' => 'Dispensación',
                                    'venta' => 'Ventas',
                                ];
                            @endphp
                            @foreach($tabs as $key => $label)
                                <a href="{{ request()->fullUrlWithQuery(['tipo' => $key === 'timeline' ? null : $key, 'page' => null]) }}" class="btn btn-sm {{ request('tipo') === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>

                    <form method="get" class="row g-2 mb-3">
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                        <div class="col-md-3">
                            <label class="form-label">Desde</label>
                            <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
                        </div>
                        <div class="col-auto align-self-end">
                            <button class="btn btn-outline-secondary" type="submit">Filtrar</button>
                        </div>
                    </form>

                    <div class="list-group list-group-flush">
                        @forelse($timeline as $event)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-start gap-3">
                                        <span class="badge bg-light text-dark text-uppercase">{{ $event['type'] }}</span>
                                        <div>
                                            <div class="fw-semibold">{{ $event['title'] }}</div>
                                            <div class="text-muted small">{{ optional($event['occurred_at'])->format('d/m/Y H:i') }}</div>
                                            <div class="text-muted">{{ $event['summary'] }}</div>
                                            @if(!empty($event['meta']))
                                                <div class="small text-muted">{{ collect($event['meta'])->filter()->map(fn($v, $k) => ucfirst($k).': '.$v)->join(' · ') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($event['url'])
                                        <a href="{{ $event['url'] }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No hay eventos para mostrar.</div>
                        @endforelse
                    </div>
                </div>
                @if($timeline instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer bg-white">{{ $timeline->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
