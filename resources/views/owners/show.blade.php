@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $owner->name }}</h1>
            <p class="text-muted mb-0">Tutor responsable</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('owners.edit', $owner) }}" class="btn btn-outline-secondary">Editar</a>
            <a href="{{ route('patients.create', ['owner_id' => $owner->id]) }}" class="btn btn-primary">Nuevo paciente</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Contacto</h6>
                    <p class="fs-5 mb-1">{{ $owner->phone }}</p>
                    <p class="text-muted mb-3">{{ $owner->email }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        @if($owner->phone)
                        <a href="tel:{{ $owner->phone }}" class="btn btn-outline-primary btn-sm">Llamar</a>
                        @endif
                        @if($owner->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $owner->whatsapp) }}" class="btn btn-success btn-sm">WhatsApp</a>
                        @endif
                        @if($owner->email)
                        <a href="mailto:{{ $owner->email }}" class="btn btn-outline-secondary btn-sm">Correo</a>
                        @endif
                    </div>
                    <div class="mt-3">
                        <div class="small text-muted">Documento</div>
                        <div>{{ $owner->document ?: 'No registrado' }}</div>
                        <div class="small text-muted mt-2">Dirección</div>
                        <div>{{ $owner->address ?: 'Sin dirección' }}</div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Notas</h6>
                    <p class="mb-0">{{ $owner->notes ?: 'Sin notas adicionales.' }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Pacientes</h5>
            </div>
            <div class="row g-3">
                @forelse($owner->patients as $patient)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex gap-3">
                            <img src="{{ $patient->photo_url }}" alt="Foto" class="rounded" style="width:80px;height:80px;object-fit:cover;">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-0">{{ $patient->display_name }}</h6>
                                        <div class="text-muted small">{{ optional($patient->species)->name }} · {{ optional($patient->breed)->name }}</div>
                                    </div>
                                    @if($patient->sexo)
                                    <span class="badge bg-light text-dark">{{ $patient->sexo }}</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-soft-primary text-primary">{{ $patient->edad ?? 'Edad N/D' }}</span>
                                    @if($patient->peso_actual)
                                        <span class="badge bg-soft-secondary text-secondary">{{ $patient->peso_actual }} kg</span>
                                    @endif
                                </div>
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary mt-3">Abrir perfil</a>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="text-muted small">Actividad reciente</div>
                            <ul class="list-unstyled mb-0">
                                @forelse($timelines[$patient->id] ?? [] as $event)
                                    <li class="d-flex align-items-start gap-2 py-1">
                                        <span class="badge bg-light text-dark text-uppercase">{{ $event['type'] }}</span>
                                        <div class="small">
                                            <div class="fw-semibold">{{ $event['title'] }}</div>
                                            <div class="text-muted">{{ optional($event['occurred_at'])->format('d/m H:i') }}</div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted small">Sin eventos recientes.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border">No hay pacientes vinculados aún.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
