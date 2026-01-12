@extends('layouts.app')

@section('content')
    @include('partials.page-header', [
        'title' => 'Dashboard médico',
        'subtitle' => 'Resumen clínico y operativo del día',
        'actions' => view('components.inline-actions', [
            'actions' => [
                ['label' => 'Nueva consulta', 'route' => route('historias-clinicas.create')],
                ['label' => 'Agendar control', 'route' => route('reservas.create'), 'variant' => 'secondary'],
                ['label' => 'Crear fórmula', 'route' => route('prescriptions.create'), 'variant' => 'ghost'],
            ],
        ]),
    ])

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <x-card title="Agenda de hoy" subtitle="Próximas 8">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['appointments'] as $appointment)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ optional($appointment->fecha)->setTimezone('America/Bogota')->format('H:i') }} · {{ optional($appointment->paciente)->nombre }}</p>
                            <p class="text-sm text-gray-500">{{ $appointment->tipocita->nombre ?? $appointment->tipo ?? 'Consulta' }} · Tutor: {{ $appointment->paciente->responsable ?? 'N/A' }}</p>
                        </div>
                        <x-badge variant="mint" :text="$appointment->estado ?? 'Agendado'" />
                    </div>
                @empty
                    <x-empty title="No hay citas hoy" description="Programa una nueva reserva para comenzar"></x-empty>
                @endforelse
            </div>
            <div class="mt-4 flex gap-2">
                <x-button variant="secondary" size="sm" href="{{ route('patients.index') }}">Abrir paciente</x-button>
                <x-button size="sm" href="{{ route('historias-clinicas.index') }}">Iniciar consulta</x-button>
            </div>
        </x-card>

        <x-card title="Hospitalización - Pendientes" :subtitle="$metrics['pendingTaskCount'] . ' pendientes'">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['pendingTasks'] as $task)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $task->title ?? 'Tarea' }}</p>
                            <p class="text-sm text-gray-500">{{ $task->stay?->cage?->name ?? 'Estancia' }} · vence {{ optional($task->end_at)->format('H:i') }}</p>
                        </div>
                        <x-badge variant="warning" :text="$task->category ?? 'Turno'" />
                    </div>
                @empty
                    <x-empty title="Sin pendientes en turno" description="Todo al día en hospitalización"></x-empty>
                @endforelse
            </div>
            <div class="mt-4">
                <x-button variant="secondary" size="sm" href="{{ route('hospital.board') }}">Ir al tablero 24/7</x-button>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        @php
            $summary = collect($metrics['hospitalSummary'])->pluck('total', 'severity');
        @endphp
        <x-kpi label="Pacientes hospitalizados" :value="$summary->sum()" :hint="'Estable: ' . ($summary['estable'] ?? 0) . ' · Observación: ' . ($summary['observacion'] ?? $summary['observación'] ?? 0) . ' · Crítico: ' . ($summary['critico'] ?? $summary['crítico'] ?? 0)" />

        <x-card title="Últimas consultas">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['recentConsultations'] as $consultation)
                    <div class="py-3">
                        <p class="font-semibold text-gray-900">{{ optional($consultation->fecha)->format('d/m H:i') }} - {{ $consultation->paciente->nombre ?? 'Paciente' }}</p>
                        <p class="text-sm text-gray-500">{{ $consultation->tipo ?? 'Consulta' }}</p>
                    </div>
                @empty
                    <x-empty title="Sin consultas recientes" description="Aún no registras consultas hoy"></x-empty>
                @endforelse
            </div>
        </x-card>

        <x-card title="Alertas rápidas">
            <div class="space-y-3">
                <div>
                    <p class="mb-2 text-sm font-semibold text-gray-800">Medicamentos por dispensar</p>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @forelse ($metrics['pendingPrescriptions'] as $prescription)
                            <li class="flex items-center justify-between">
                                <span>Receta #{{ $prescription->id }}</span>
                                <x-badge variant="warning" :text="$prescription->status ?? 'Pendiente'" />
                            </li>
                        @empty
                            <li class="text-gray-500">Sin fórmulas pendientes</li>
                        @endforelse
                    </ul>
                </div>
                <div>
                    <p class="mb-2 text-sm font-semibold text-gray-800">Pacientes con control vencido</p>
                    <ul class="space-y-1 text-sm text-gray-600">
                        @forelse ($metrics['overdueControls'] as $patient)
                            <li class="flex items-center justify-between">
                                <span>{{ $patient->nombre ?? 'Paciente' }}</span>
                                <x-badge variant="gray" :text="optional($patient->proximo_control_at)->format('d/m') ?? 'Fecha N/D'" />
                            </li>
                        @empty
                            <li class="text-gray-500">Sin alertas de control</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </x-card>
    </div>

    <x-card title="Acciones rápidas" class="mt-6">
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('historias-clinicas.create') }}">Nueva consulta</x-button>
            @if (Route::has('hospital.stays.create'))
                <x-button variant="secondary" href="{{ route('hospital.stays.create') }}">Hospitalizar paciente</x-button>
            @endif
            <x-button variant="ghost" href="{{ route('prescriptions.create') }}">Crear fórmula</x-button>
            <x-button variant="secondary" href="{{ route('reservas.create') }}">Agendar control</x-button>
        </div>
    </x-card>
@endsection
