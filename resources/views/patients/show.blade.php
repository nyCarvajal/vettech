@extends('layouts.app')

@push('styles')
<style>
    :root {
        --lavender-600: #7c6ff2;
        --lavender-100: #f1edff;
        --mint-600: #3fc6b8;
        --mint-100: #e6f8f5;
        --ink-900: #0f172a;
        --ink-600: #475569;
        --surface: #ffffff;
    }

    .patient-dashboard {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .dashboard-title {
        color: var(--ink-900);
        margin: 0;
        font-weight: 700;
    }

    .dashboard-subtitle {
        color: var(--ink-600);
        margin: 0.1rem 0 0;
    }

    .pill-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 9999px;
        border: 1px solid rgba(124, 111, 242, 0.25);
        color: var(--lavender-600);
        background: linear-gradient(135deg, #f8f7ff, #eef3ff);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .pill-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 30px rgba(124, 111, 242, 0.18);
    }

    .grid-panels {
        display: grid;
        grid-template-columns: 1.65fr 1fr;
        gap: 1rem;
    }

    .panel-card {
        background: var(--surface);
        border: 1px solid #edf0f7;
        border-radius: 18px;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .panel-body {
        padding: 1.25rem 1.5rem;
    }

    .hero-card .panel-body,
    .owner-panel .panel-body {
        padding: 0.9rem 1rem;
    }

    .hero-card {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f4f1ff, #e7f8f4);
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.7rem;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.65);
        color: var(--ink-600);
        font-weight: 600;
        border: 1px solid rgba(63, 198, 184, 0.2);
    }

    .hero-layout {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 0.75rem;
    }

    .avatar-lg {
        width: 90px;
        height: 90px;
        border-radius: 20px;
        object-fit: cover;
        border: 5px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 12px 30px rgba(124, 111, 242, 0.15);
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.65rem;
        border-radius: 10px;
        background: rgba(124, 111, 242, 0.08);
        color: var(--lavender-600);
        font-weight: 600;
        margin-right: 0.4rem;
        margin-bottom: 0.4rem;
    }

    .tag.mint {
        background: rgba(63, 198, 184, 0.1);
        color: var(--mint-600);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.6rem;
        margin-top: 0.75rem;
    }

    .info-tile {
        padding: 0.6rem 0.85rem;
        border-radius: 12px;
        border: 1px solid #edf0f7;
        background: rgba(255, 255, 255, 0.8);
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--ink-600);
        margin: 0;
    }

    .info-value {
        font-weight: 700;
        color: var(--ink-900);
        margin: 0.15rem 0 0;
    }

    .section-title {
        font-weight: 700;
        margin: 0;
        color: var(--ink-900);
    }

    .section-subtitle {
        color: var(--ink-600);
        margin: 0.2rem 0 0;
    }

    .owner-card {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.65rem;
    }

    .contact-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.7rem;
        background: rgba(63, 198, 184, 0.12);
        color: var(--ink-900);
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
    }

    .last-visit-card {
        background: linear-gradient(135deg, #6f62f6, #43ccb8);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .last-visit-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.15), transparent 40%);
    }

    .last-visit-content {
        position: relative;
        z-index: 1;
    }

    .last-visit-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        margin-top: 1rem;
    }

    .history-item {
        display: grid;
        grid-template-columns: 140px 1fr auto;
        gap: 0.75rem;
        align-items: center;
        padding: 0.9rem 1rem;
        border-radius: 14px;
        border: 1px solid #edf0f7;
        background: #fff;
    }

    .history-date {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        color: var(--ink-600);
        font-weight: 600;
    }

    .history-title {
        font-weight: 700;
        margin: 0;
        color: var(--ink-900);
    }

    .history-meta {
        color: var(--ink-600);
        margin: 0.25rem 0 0;
    }

    .history-actions a {
        color: var(--lavender-600);
        text-decoration: none;
        font-weight: 600;
    }

    .badge-soft {
        padding: 0.3rem 0.65rem;
        border-radius: 12px;
        background: rgba(124, 111, 242, 0.12);
        color: var(--lavender-600);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    @media (max-width: 1100px) {
        .grid-panels {
            grid-template-columns: 1fr;
        }
        .history-item {
            grid-template-columns: 1fr;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="patient-dashboard">
    <div class="dashboard-header">
        <div>
            <p class="dashboard-subtitle mb-1">Paciente / {{ $patient->display_name ?: 'Sin nombre' }}</p>
            <h1 class="dashboard-title">Panel clínico del paciente</h1>
        </div>
        <div class="flex gap-2">
            @if(!$activeStay)
                <a href="{{ route('hospital.stays.create', ['patient_id' => $patient->id]) }}" class="pill-action">Hospitalizar paciente</a>
            @endif
            <a href="{{ route('patients.edit', $patient) }}" class="pill-action">Editar ficha</a>
            <a href="{{ route('patients.index') }}" class="pill-action">Volver al listado</a>
        </div>
    </div>

    <div class="grid-panels">
        <div class="panel-card hero-card">
            <div class="panel-body">
                <div class="hero-layout">
                    <img src="{{ $patient->photo_url }}" alt="Foto" class="avatar-lg">
                    <div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="hero-badge">Paciente</span>
                            @if($activeStay)
                                <span class="tag">Hospitalizado</span>
                            @endif
                        </div>
                        <h2 class="h4 mb-1">{{ $patient->display_name ?: 'Paciente sin nombre' }}</h2>
                        <p class="text-muted mb-2">{{ optional($patient->species)->name }} · {{ optional($patient->breed)->name }}</p>
                        <div class="d-flex flex-wrap gap-2">
                            @if($patient->edad)
                                <span class="tag mint">{{ $patient->edad }}</span>
                            @endif
                            @if($patient->sexo)
                                <span class="tag">Sexo: {{ $patient->sexo }}</span>
                            @endif
                            <span class="tag mint">Peso: {{ $patient->peso_formateado ?? 'N/D' }}</span>
                        </div>

                        <div class="info-grid">
                            <div class="info-tile">
                                <p class="info-label">Estado</p>
                                <p class="info-value">{{ $patient->estado ?? 'N/D' }}</p>
                            </div>
                            <div class="info-tile">
                                <p class="info-label">Microchip</p>
                                <p class="info-value">{{ $patient->microchip ?: 'N/A' }}</p>
                            </div>
                            <div class="info-tile">
                                <p class="info-label">Temperamento</p>
                                <p class="info-value">{{ $patient->temperamento ?: 'Sin registrar' }}</p>
                            </div>
                            <div class="info-tile">
                                <p class="info-label">Alergias</p>
                                <p class="info-value">{{ $patient->alergias ?: 'Sin alergias registradas' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-card owner-panel">
            <div class="panel-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Tutor responsable</p>
                        <h3 class="section-title">{{ optional($patient->owner)->name ?: 'Sin tutor asignado' }}</h3>
                        <p class="section-subtitle">{{ optional($patient->owner)->address }}</p>
                    </div>
                    <a href="{{ $patient->owner ? route('owners.show', $patient->owner) : '#' }}" class="pill-action" @if(!$patient->owner) aria-disabled="true" @endif>Ver ficha</a>
                </div>
                <div class="owner-card mt-3">
                    <div class="info-grid">
                        <div class="info-tile">
                            <p class="info-label">Teléfono</p>
                            <p class="info-value">{{ optional($patient->owner)->phone ?: 'N/D' }}</p>
                        </div>
                        <div class="info-tile">
                            <p class="info-label">WhatsApp</p>
                            <p class="info-value">{{ optional($patient->owner)->whatsapp ?: 'N/D' }}</p>
                        </div>
                        <div class="info-tile">
                            <p class="info-label">Correo</p>
                            <p class="info-value">{{ optional($patient->owner)->email ?: 'N/D' }}</p>
                        </div>
                        <div class="info-tile">
                            <p class="info-label">Relación</p>
                            <p class="info-value">{{ optional($patient->owner)->relationship ?: 'Tutor principal' }}</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        @if(optional($patient->owner)->whatsapp)
                            <a class="contact-chip" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $patient->owner->whatsapp) }}">WhatsApp</a>
                        @endif
                        @if(optional($patient->owner)->phone)
                            <a class="contact-chip" href="tel:{{ $patient->owner->phone }}">Llamar</a>
                        @endif
                        @if(optional($patient->owner)->email)
                            <a class="contact-chip" href="mailto:{{ $patient->owner->email }}">Correo</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-panels">
        <div class="panel-card last-visit-card">
            <div class="panel-body last-visit-content">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="text-uppercase small mb-1" style="letter-spacing: 0.08em; opacity: 0.9;">Última consulta</p>
                        <h3 class="h4 mb-2">{{ $patient->lastEncounter ? optional($patient->lastEncounter->occurred_at)->format('d M Y') : 'Sin consultas' }}</h3>
                        <p class="mb-0" style="opacity: 0.9;">{{ $patient->lastEncounter->motivo ?? 'Aún no hay motivo registrado' }}</p>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a href="#" class="pill-action" style="border-color: rgba(255,255,255,0.5); color: #fff; background: rgba(255,255,255,0.12);">Ver historia</a>
                        <a
                            href="{{ route('historias-clinicas.create', ['paciente_id' => $patient->id]) }}"
                            class="pill-action"
                            style="border-color: rgba(255,255,255,0.5); color: #fff; background: rgba(255,255,255,0.12);"
                        >
                            Nueva consulta
                        </a>
                    </div>
                </div>
                @if($patient->lastEncounter)
                    <div class="last-visit-grid mt-3">
                        <div>
                            <p class="mb-1 fw-semibold">Diagnóstico</p>
                            <p class="mb-0">{{ $patient->lastEncounter->diagnostico ?? 'N/D' }}</p>
                        </div>
                        <div>
                            <p class="mb-1 fw-semibold">Plan</p>
                            <p class="mb-0">{{ $patient->lastEncounter->plan ?? 'Sin plan' }}</p>
                        </div>
                        <div>
                            <p class="mb-1 fw-semibold">Peso / Temp</p>
                            <p class="mb-0">{{ $patient->lastEncounter->peso ? $patient->lastEncounter->peso.' kg' : 'N/D' }} · {{ $patient->lastEncounter->temperatura ? $patient->lastEncounter->temperatura.'°C' : 'N/D' }}</p>
                        </div>
                    </div>
                @else
                    <p class="mb-0" style="opacity: 0.8;">Aún no hay consultas registradas para este paciente.</p>
                @endif
            </div>
        </div>

        <div class="panel-card">
            <div class="panel-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Notas rápidas</p>
                        <h3 class="section-title">Observaciones clínicas</h3>
                    </div>
                </div>
                <p class="section-subtitle mb-3">Información clave para el equipo médico.</p>
                <div class="info-grid">
                    <div class="info-tile">
                        <p class="info-label">Observaciones</p>
                        <p class="info-value">{{ $patient->observaciones ?: 'Sin notas adicionales.' }}</p>
                    </div>
                    <div class="info-tile">
                        <p class="info-label">Última actualización</p>
                        <p class="info-value">{{ $patient->updated_at ? $patient->updated_at->format('d M Y') : 'N/D' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="section-title mb-1">Historial de visitas</h3>
                    <p class="section-subtitle">Consultas, peluquería, hospitalización y ventas recientes.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $tabs = [
                            'timeline' => 'Todo',
                            'historia' => 'Historias clínicas',
                            'consulta' => 'Consultas',
                            'banio' => 'Peluquería',
                            'hospital' => 'Hospital',
                            'dispensacion' => 'Dispensación',
                            'venta' => 'Ventas',
                        ];
                    @endphp
                    @foreach($tabs as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => $key === 'timeline' ? null : $key, 'page' => null]) }}" class="badge-soft {{ request('tipo') === $key ? 'active' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <form method="get" class="row g-2 mt-3">
                <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                <div class="col-md-3">
                    <label class="form-label text-muted">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
                </div>
                <div class="col-auto align-self-end">
                    <button class="pill-action" type="submit">Filtrar</button>
                </div>
            </form>

            <div class="history-list">
                @forelse($timeline as $event)
                    <div class="history-item">
                        <div class="history-date">
                            <span>{{ optional($event['occurred_at'])->format('d/m/Y') }}</span>
                            <span class="text-muted small">{{ optional($event['occurred_at'])->format('H:i') }}</span>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge-soft">{{ strtoupper($event['type']) }}</span>
                                <h5 class="history-title mb-0">{{ $event['title'] }}</h5>
                            </div>
                            <p class="history-meta mb-0">{{ $event['summary'] }}</p>
                            @if(!empty($event['meta']))
                                <p class="history-meta mb-0">{{ collect($event['meta'])->filter()->map(fn($v, $k) => ucfirst($k).': '.$v)->join(' · ') }}</p>
                            @endif
                        </div>
                        <div class="history-actions">
                            @if($event['url'])
                                <a href="{{ $event['url'] }}">Ver detalle</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No hay eventos para mostrar.</p>
                @endforelse
            </div>
        </div>
        @if($timeline instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer bg-white">{{ $timeline->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
