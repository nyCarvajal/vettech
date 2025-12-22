@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard médico</h2>
            <small class="text-muted">Resumen clínico y operativo del día</small>
        </div>
        <div class="btn-group">
            <a href="{{ route('historias-clinicas.create') }}" class="btn btn-primary">Nueva consulta</a>
            <a href="{{ route('reservas.create') }}" class="btn btn-outline-secondary">Agendar control</a>
            <a href="{{ route('prescriptions.create') }}" class="btn btn-outline-secondary">Crear fórmula</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Agenda de hoy</span>
                    <small class="text-muted">Próximas 8</small>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['appointments'] as $appointment)
                        @include('dashboards._list_item', [
                            'title' => optional($appointment->fecha)->setTimezone('America/Bogota')->format('H:i') . ' - ' . optional($appointment->paciente)->nombre,
                            'subtitle' => ($appointment->tipocita->nombre ?? $appointment->tipo ?? 'Consulta') . ' · Tutor: ' . ($appointment->paciente->responsable ?? 'N/A'),
                            'meta' => ''
                        ])
                    @empty
                        <li class="list-group-item text-muted">No hay citas hoy</li>
                    @endforelse
                </ul>
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-primary btn-sm">Abrir paciente</a>
                    <a href="{{ route('historias-clinicas.index') }}" class="btn btn-primary btn-sm">Iniciar consulta</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Hospitalización - Pendientes</span>
                    <span class="badge bg-light text-dark">{{ $metrics['pendingTaskCount'] }} pendientes</span>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['pendingTasks'] as $task)
                        @include('dashboards._list_item', [
                            'title' => $task->title ?? 'Tarea',
                            'subtitle' => ($task->stay?->cage?->name ?? 'Estancia') . ' · vence ' . optional($task->end_at)->format('H:i'),
                            'meta' => $task->category ?? 'turno'
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin pendientes en turno</li>
                    @endforelse
                </ul>
                <div class="card-footer">
                    <a href="{{ route('hospital.board') }}" class="btn btn-outline-primary btn-sm">Ir al tablero 24/7</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            @php
                $summary = collect($metrics['hospitalSummary'])->pluck('total', 'severity');
            @endphp
            @include('dashboards._kpi_card', [
                'title' => 'Pacientes hospitalizados',
                'value' => $summary->sum(),
                'subtitle' => 'Estable: ' . ($summary['estable'] ?? 0) . ' · Observación: ' . ($summary['observacion'] ?? $summary['observación'] ?? 0) . ' · Crítico: ' . ($summary['critico'] ?? $summary['crítico'] ?? 0),
            ])
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">Últimas consultas</div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['recentConsultations'] as $consultation)
                        @include('dashboards._list_item', [
                            'title' => optional($consultation->fecha)->format('d/m H:i') . ' - ' . ($consultation->paciente->nombre ?? 'Paciente'),
                            'subtitle' => $consultation->tipo ?? 'Consulta',
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin consultas recientes</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">Alertas rápidas</div>
                <div class="card-body">
                    <p class="mb-2 fw-semibold">Medicamentos por dispensar</p>
                    <ul class="list-unstyled small mb-3">
                        @forelse ($metrics['pendingPrescriptions'] as $prescription)
                            <li class="mb-1">Receta #{{ $prescription->id }} - {{ $prescription->status ?? 'Pendiente' }}</li>
                        @empty
                            <li class="text-muted">Sin fórmulas pendientes</li>
                        @endforelse
                    </ul>
                    <p class="mb-2 fw-semibold">Pacientes con control vencido</p>
                    <ul class="list-unstyled small mb-0">
                        @forelse ($metrics['overdueControls'] as $patient)
                            <li class="mb-1">{{ $patient->nombre ?? 'Paciente' }} · {{ optional($patient->proximo_control_at)->format('d/m') }}</li>
                        @empty
                            <li class="text-muted">Sin alertas de control</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Acciones rápidas</div>
        <div class="card-body d-flex flex-wrap gap-2">
            <a href="{{ route('historias-clinicas.create') }}" class="btn btn-primary">Nueva consulta</a>
            <a href="{{ route('hospital.stays.create') }}" class="btn btn-outline-primary">Hospitalizar paciente</a>
            <a href="{{ route('prescriptions.create') }}" class="btn btn-outline-secondary">Crear fórmula</a>
            <a href="{{ route('reservas.create') }}" class="btn btn-secondary">Agendar control</a>
        </div>
    </div>
</div>
@endsection
