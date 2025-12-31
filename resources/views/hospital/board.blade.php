@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-emerald-800 uppercase tracking-wider">Hospitalización 24/7</p>
            <h1 class="text-2xl font-bold text-gray-900">Tablero de hospital</h1>
            <p class="text-sm text-gray-500">Da click en una tarjeta para abrir la ficha y registrar tratamientos, dosis y signos vitales.</p>
        </div>
        <div class="flex items-center gap-2 text-emerald-700 bg-emerald-50 px-4 py-2 rounded-full border border-emerald-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l3 3" />
            </svg>
            <span class="text-sm font-semibold">Actualizado {{ now()->format('d M H:i') }}</span>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach($cages as $cage)
            <div class="rounded-xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-emerald-100">Jaula</p>
                        <p class="text-lg font-semibold">{{ $cage->name }}</p>
                        <p class="text-xs text-emerald-100">{{ $cage->location }}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                        <span>{{ $cage->stays->count() }} ocupado</span>
                    </div>
                </div>

                <div class="p-4 space-y-3">
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
                        <a href="{{ route('hospital.show', $stay) }}" class="block rounded-lg border border-emerald-100 bg-emerald-50/50 hover:bg-emerald-100 transition shadow-sm">
                            <div class="flex items-start gap-3 p-3">
                                <div class="shrink-0 w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l3 3m4 3H5a1 1 0 01-1-1V6a1 1 0 011-1h4l2-2h2l2 2h4a1 1 0 011 1v11a1 1 0 01-1 1z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-500">Paciente</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ $stay->patient->name ?? 'Paciente #' . $stay->patient_id }}</p>
                                            <p class="text-sm text-gray-500">Tutor: {{ $stay->owner->name ?? 'Sin registrar' }}</p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border {{ $severityClass }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $severityLabel ?: 'Sin severidad' }}
                                        </span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-6 4h6M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2h-2V3H8v2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Ingreso: {{ optional($stay->admitted_at)->format('d/m H:i') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.5 19h7a4.5 4.5 0 004.5-4.5V9a4.5 4.5 0 00-4.5-4.5h-7A4.5 4.5 0 004 9v5.5A4.5 4.5 0 008.5 19z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6" />
                                            </svg>
                                            <span>{{ $stay->tasks->count() }} tareas pendientes</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 12h4.5m-2.25-2.25v4.5" />
                                            </svg>
                                            <span>Plan: {{ $stay->plan ?: 'No definido' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M9 6v12m6-12v12M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                            <span>Dx: {{ $stay->primary_dx ?: 'Pendiente' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between px-3 py-2 border-t border-emerald-100 bg-white/60 text-sm text-emerald-700">
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
