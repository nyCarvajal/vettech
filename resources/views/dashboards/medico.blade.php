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

    @php
        $kpis = [
            [
                'label' => 'Pacientes hoy',
                'value' => $metrics['patientsToday'] ?? '18',
                'accent' => 'purple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0 17.933 17.933 0 01-7.499 1.632c-2.6 0-5.064-.568-7.499-1.632z"/></svg>',
                'hint' => 'Ingresados y atendidos',
            ],
            [
                'label' => 'Citas pendientes',
                'value' => $metrics['pendingAppointments'] ?? '06',
                'accent' => 'mint',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 9.75h15M19.5 8.25V18a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18V8.25m15 0A2.25 2.25 0 0017.25 6H6.75A2.25 2.25 0 004.5 8.25"/></svg>',
                'hint' => 'Incluye controles y grooming',
            ],
            [
                'label' => 'Hospitalizados',
                'value' => $metrics['hospitalized'] ?? '12',
                'accent' => 'blue',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l.75-8.25a3 3 0 012.992-2.7h7.516a3 3 0 012.992 2.7l.75 8.25M9.75 9V5.25A2.25 2.25 0 0112 3h0a2.25 2.25 0 012.25 2.25V9m-7.5 0h10.5"/></svg>',
                'hint' => 'Camas ocupadas / monitoreo',
            ],
            [
                'label' => 'Ventas hoy',
                'value' => $metrics['salesToday'] ?? '$ 2.4M',
                'accent' => 'amber',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/><circle cx="12" cy="12" r="8.25" stroke-width="1.5" stroke="currentColor" fill="none" /></svg>',
                'hint' => 'Servicios y dispensación',
            ],
        ];
    @endphp

    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpis as $kpi)
            <x-mini-kpi :label="$kpi['label']" :value="$kpi['value']" :icon="$kpi['icon']" :accent="$kpi['accent']" :hint="$kpi['hint']" />
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mt-8">
        <x-card title="Agenda de hoy" subtitle="Próximas 8" class="pt-6">
            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-purple-400 via-purple-300 to-emerald-300"></div>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600 shadow-inner shadow-white/60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 9.75h15M5.25 6.75h13.5a1.5 1.5 0 011.5 1.5v10.5a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5z"/></svg>
                </div>
                <div class="text-sm text-slate-500">Consultas, controles y grooming programados</div>
            </div>
            <div class="divide-y divide-gray-100/80">
                @forelse ($metrics['appointments'] as $appointment)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ optional($appointment->fecha)->setTimezone('America/Bogota')->format('H:i') }} · {{ optional($appointment->paciente)->nombre }}</p>
                            <p class="text-sm text-gray-500">{{ $appointment->tipocita->nombre ?? $appointment->tipo ?? 'Consulta' }} · Tutor: {{ $appointment->paciente->responsable ?? 'N/A' }}</p>
                        </div>
                        <x-badge variant="mint" :text="$appointment->estado ?? 'Agendado'" />
                    </div>
                @empty
                    <x-empty title="No hay citas hoy" description="Programa una nueva reserva para comenzar">
                        <x-button size="sm" href="{{ route('reservas.create') }}">Agendar cita</x-button>
                        <x-button variant="ghost" size="sm" href="{{ route('historias-clinicas.create') }}">Iniciar consulta</x-button>
                    </x-empty>
                @endforelse
            </div>
            <div class="mt-5 flex flex-wrap gap-2">
                <x-button variant="secondary" size="sm" href="{{ route('pacientes.index') }}">Abrir paciente</x-button>
                <x-button size="sm" href="{{ route('historias-clinicas.index') }}">Iniciar consulta</x-button>
            </div>
        </x-card>

        <x-card title="Hospitalización - Pendientes" :subtitle="$metrics['pendingTaskCount'] . ' pendientes'" class="bg-emerald-50/60 border-emerald-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 shadow-inner shadow-white/60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75L7.5 5.25m9 1.5L18 5.25M9 17.25l-1.5 1.5m9-1.5l1.5 1.5M3.75 9h16.5m-15 6h13.5M4.5 7.5A1.5 1.5 0 016 6h12a1.5 1.5 0 011.5 1.5v9A1.5 1.5 0 0118 18H6a1.5 1.5 0 01-1.5-1.5v-9z"/></svg>
                </div>
                <div class="text-sm text-slate-600">Tareas de turno, medicamentos y signos vitales</div>
            </div>
            <div class="divide-y divide-emerald-100/80">
                @forelse ($metrics['pendingTasks'] as $task)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $task->title ?? 'Tarea' }}</p>
                            <p class="text-sm text-gray-600">{{ $task->stay?->cage?->name ?? 'Estancia' }} · vence {{ optional($task->end_at)->format('H:i') }}</p>
                        </div>
                        <x-badge variant="warning" :text="$task->category ?? 'Turno'" />
                    </div>
                @empty
                    <x-empty title="Sin pendientes en turno" description="Todo al día en hospitalización">
                        <x-button size="sm" href="{{ route('hospital.stays.create') }}">Hospitalizar paciente</x-button>
                        <x-button variant="ghost" size="sm" href="{{ route('hospital.board') }}">Ver tablero 24/7</x-button>
                    </x-empty>
                @endforelse
            </div>
            <div class="mt-5">
                <x-button variant="secondary" size="sm" href="{{ route('hospital.board') }}">Ir al tablero 24/7</x-button>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3 mt-8">
        @php
            $summary = collect($metrics['hospitalSummary'])->pluck('total', 'severity');
        @endphp
        <x-kpi label="Pacientes hospitalizados" :value="$summary->sum()" :hint="'Estable: ' . ($summary['estable'] ?? 0) . ' · Observación: ' . ($summary['observacion'] ?? $summary['observación'] ?? 0) . ' · Crítico: ' . ($summary['critico'] ?? $summary['crítico'] ?? 0)" />

        <x-card title="Últimas consultas" class="bg-white/90">
            <div class="divide-y divide-gray-100/80">
                @forelse ($metrics['recentConsultations'] as $consultation)
                    <div class="py-3">
                        <p class="font-semibold text-gray-900">{{ optional($consultation->fecha)->format('d/m H:i') }} - {{ $consultation->paciente->nombre ?? 'Paciente' }}</p>
                        <p class="text-sm text-gray-500">{{ $consultation->tipo ?? 'Consulta' }}</p>
                    </div>
                @empty
                    <x-empty title="Sin consultas recientes" description="Aún no registras consultas hoy">
                        <x-button size="sm" href="{{ route('historias-clinicas.create') }}">Registrar consulta</x-button>
                    </x-empty>
                @endforelse
            </div>
        </x-card>

        <x-card title="Alertas rápidas" class="bg-white/90">
            <div class="space-y-4">
                <div class="rounded-xl border border-slate-100/80 bg-slate-50/60 p-3">
                    <p class="mb-2 text-sm font-semibold text-gray-800">Medicamentos por dispensar</p>
                    <ul class="space-y-2 text-sm text-gray-600">
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
                <div class="rounded-xl border border-slate-100/80 bg-slate-50/60 p-3">
                    <p class="mb-2 text-sm font-semibold text-gray-800">Pacientes con control vencido</p>
                    <ul class="space-y-2 text-sm text-gray-600">
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

    <x-card title="Acciones rápidas" class="mt-8">
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
