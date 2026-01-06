@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6 bg-gradient-to-r from-emerald-50 via-white to-cyan-50 border border-emerald-100 rounded-2xl px-5 py-4 shadow-sm">
        <div>
            <p class="text-sm text-emerald-800 uppercase tracking-wider">Hospitalización 24/7</p>
            <h1 class="text-2xl font-bold text-gray-900">Tablero de hospital</h1>
            <p class="text-sm text-gray-600">Da click en una tarjeta para abrir la ficha y registrar tratamientos, dosis y signos vitales.</p>
        </div>
        <div class="flex items-center gap-2 text-emerald-800 bg-white px-4 py-2 rounded-full border border-emerald-100 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l3 3" />
            </svg>
            <span class="text-sm font-semibold">Actualizado {{ now()->format('d M H:i') }}</span>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach($cages as $cage)
            <div class="rounded-xl border border-emerald-100 bg-white shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-emerald-100">Jaula</p>
                        <p class="text-lg font-semibold break-words">{{ $cage->name }}</p>
                        <p class="text-xs text-emerald-100 break-words">{{ $cage->location }}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                        <span>{{ $cage->stays->count() }} ocupado</span>
                    </div>
                </div>

                <div class="p-4 space-y-3 bg-gradient-to-b from-emerald-50/60 via-white to-cyan-50/60">
                    @forelse($cage->stays as $stay)
                        @php
                            $severityColors = [
                                'critico' => 'bg-red-100 text-red-700 border-red-200',
                                'estable' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'observacion' => 'bg-amber-100 text-amber-700 border-amber-200',
                            ];
                            $severityLabel = ucfirst($stay->severity ?? '');
                            $severityClass = $severityColors[$stay->severity] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                        @endphp
                        <a href="{{ route('hospital.show', $stay) }}" class="block rounded-xl border border-emerald-200 bg-white/90 hover:bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex flex-col gap-4 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l3 3m4 3H5a1 1 0 01-1-1V6a1 1 0 011-1h4l2-2h2l2 2h4a1 1 0 011 1v11a1 1 0 01-1 1z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-2">
                                        <div class="flex flex-wrap items-start gap-3">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs text-emerald-700 font-semibold uppercase">Paciente</p>
                                                <p class="text-lg font-bold text-gray-900 leading-tight break-words">{{ $stay->patient->display_name ?? 'Paciente #' . $stay->patient_id }}</p>
                                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-700">
                                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V7a2 2 0 00-2-2h-3l-1-2H9L8 5H5a2 2 0 00-2 2v10a2 2 0 002 2h5" />
                                                        </svg>
                                                        <span class="min-w-0 break-words">{{ optional($stay->patient->species)->name ?? 'Especie' }}</span>
                                                    </span>
                                                    @if($stay->patient?->breed)
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-cyan-50 text-cyan-700 border border-cyan-100 break-words">{{ $stay->patient->breed->name }}</span>
                                                    @endif
                                                    @if($stay->patient?->edad)
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100 break-words">{{ $stay->patient->edad }}</span>
                                                    @endif
                                                    @if($stay->patient?->peso_formateado)
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-100 break-words">{{ $stay->patient->peso_formateado }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border {{ $severityClass }} shadow-inner">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $severityLabel ?: 'Sin severidad' }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-6 4h6M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2h-2V3H8v2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="break-words">Ingreso: {{ optional($stay->admitted_at)->format('d/m H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 min-w-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.5 19h7a4.5 4.5 0 004.5-4.5V9a4.5 4.5 0 00-4.5-4.5h-7A4.5 4.5 0 004 9v5.5A4.5 4.5 0 008.5 19z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6" />
                                                </svg>
                                                <span class="break-words">{{ $stay->tasks->count() }} tareas pendientes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="rounded-lg border border-emerald-100 bg-emerald-50/70 px-3 py-3 text-sm text-emerald-800 flex items-start gap-2 shadow-inner min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11.5c0 4.694-3.134 8.5-7 8.5s-7-3.806-7-8.5S8.134 3 12 3s7 3.806 7 8.5z" />
                                        </svg>
                                        <div class="min-w-0 space-y-1">
                                            <p class="text-xs uppercase tracking-wide text-emerald-700">Tutor</p>
                                            <p class="font-semibold break-words">{{ $stay->owner->name ?? 'Sin registrar' }}</p>
                                            <div class="text-xs text-emerald-700 flex flex-col gap-1 break-words">
                                                @if($stay->owner?->phone)
                                                    <span class="inline-flex items-center gap-1 min-w-0"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-1.272.636a11.042 11.042 0 005.516 5.516l.636-1.272a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.82 21 3 14.18 3 6V5z"/></svg>{{ $stay->owner->phone }}</span>
                                                @endif
                                                @if($stay->owner?->email)
                                                    <span class="inline-flex items-center gap-1 min-w-0"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>{{ $stay->owner->email }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-lg border border-cyan-100 bg-cyan-50/70 px-3 py-3 text-sm text-cyan-800 flex items-start gap-2 shadow-inner min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 0 0118 0z" />
                                        </svg>
                                        <div class="min-w-0 space-y-1">
                                            <p class="text-xs uppercase tracking-wide text-cyan-700">Seguimiento</p>
                                            <p class="font-semibold break-words">{{ $stay->tasks->count() }} tareas pendientes</p>
                                            <p class="text-xs text-cyan-700 break-words">Plan: {{ $stay->plan ?: 'No definido' }}</p>
                                        </div>
                                    </div>
                                    <div class="rounded-lg border border-amber-100 bg-amber-50/70 px-3 py-3 text-sm text-amber-800 flex items-start gap-2 shadow-inner min-w-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M9 6v12m6-12v12M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <div class="min-w-0 space-y-1">
                                            <p class="text-xs uppercase tracking-wide text-amber-700">Diagnóstico</p>
                                            <p class="font-semibold break-words">Dx: {{ $stay->primary_dx ?: 'Pendiente' }}</p>
                                            <p class="text-xs text-amber-700 break-words">Notas rápidas: {{ $stay->plan ?: 'Sin plan registrado' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between px-4 py-2 border-t border-emerald-100 bg-gradient-to-r from-emerald-50 to-white text-sm text-emerald-700 rounded-b-xl">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l6-6 6 6m-6-6v12m0 0l6-6m-6 6l-6-6" />
                                    </svg>
                                    <span class="font-semibold">Ver ficha</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Libre • Asigna un paciente para hospitalizar</span>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
