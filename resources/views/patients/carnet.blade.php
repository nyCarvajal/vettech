@extends('layouts.app')

@push('styles')
<style>
    .gradient-bg { background: linear-gradient(135deg, #ede9fe, #e0f7f4); }
    .glass-card { background: rgba(255,255,255,0.82); backdrop-filter: blur(10px); border:1px solid rgba(124,111,242,0.15); }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-slate-500">Paciente / {{ $patient->display_name }}</p>
            <h1 class="text-2xl font-bold text-slate-900">Carnet digital de vacunación y desparasitación</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 rounded-full text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:shadow">Resumen</a>
            <a href="{{ route('patients.carnet.pdf', $patient) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-gradient-to-r from-purple-500 to-emerald-400 shadow-lg">Descargar PDF</a>
        </div>
    </div>

    <div class="p-6 rounded-2xl gradient-bg">
        <div class="flex items-center gap-4">
            <img src="{{ $patient->photo_url }}" class="w-16 h-16 rounded-2xl object-cover shadow-lg" alt="Foto">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">{{ $patient->display_name }}</h2>
                <p class="text-sm text-slate-600">{{ optional($patient->species)->name }} · {{ optional($patient->breed)->name }}</p>
                <p class="text-sm text-slate-600">Tutor: {{ optional($patient->owner)->name }}</p>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-4 shadow flex flex-wrap gap-3 items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Acciones rápidas</p>
            <h3 class="text-lg font-semibold text-slate-900">Registrar atención preventiva</h3>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('patients.immunizations.create', $patient) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-purple-500 hover:bg-purple-600 shadow">
                Registrar vacuna
            </a>
            <a href="{{ route('patients.dewormings.create', [$patient, 'internal']) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 shadow">
                Registrar interna
            </a>
            <a href="{{ route('patients.dewormings.create', [$patient, 'external']) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 shadow">
                Registrar externa
            </a>
        </div>
    </div>

    <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="hidden" name="tab" value="carnet">
        <div>
            <label class="text-xs text-slate-500">Desde</label>
            <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border-slate-200" />
        </div>
        <div>
            <label class="text-xs text-slate-500">Hasta</label>
            <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border-slate-200" />
        </div>
        <div class="md:col-span-2">
            <label class="text-xs text-slate-500">Buscar por producto o nombre</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rabia, ivermectina, etc" class="w-full rounded-xl border-slate-200" />
        </div>
        <div class="md:col-span-4 flex justify-end gap-3">
            <a href="{{ route('patients.carnet', $patient) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700">Limpiar</a>
            <button type="submit" class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-purple-500 to-emerald-400 shadow">Aplicar filtros</button>
        </div>
    </form>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="glass-card rounded-2xl p-5 shadow">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Vacunas</p>
                    <h3 class="text-lg font-semibold text-slate-900">Historial de inmunizaciones</h3>
                </div>
                <a href="{{ route('patients.immunizations.create', $patient) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-purple-500 hover:bg-purple-600">Registrar vacuna</a>
            </div>
            <div class="space-y-3">
                @forelse($immunizations as $vaccine)
                    @php
                        $statusColor = match(true) {
                            $vaccine->status === 'overdue' => 'bg-red-100 text-red-700',
                            $vaccine->status === 'scheduled' => 'bg-amber-100 text-amber-700',
                            default => 'bg-emerald-100 text-emerald-700',
                        };
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-100 flex justify-between items-start">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">{{ ucfirst($vaccine->status) }}</span>
                                @if($vaccine->contains_rabies)
                                    <span class="px-2 py-1 rounded-md text-[11px] bg-rose-100 text-rose-700">Contiene rabia</span>
                                @endif
                            </div>
                            <p class="text-lg font-semibold text-slate-900">{{ $vaccine->vaccine_name }}</p>
                            <p class="text-sm text-slate-600">Producto: {{ $vaccine->item->nombre ?? $vaccine->item_manual }}</p>
                            <p class="text-xs text-slate-500">Lote: {{ $vaccine->batch_lot }} · Aplicada: {{ optional($vaccine->applied_at)->format('d/m/Y') }}</p>
                            @if($vaccine->next_due_at)
                                <p class="text-xs text-slate-500">Próxima: {{ optional($vaccine->next_due_at)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2 text-right">
                            <a href="{{ route('patients.immunizations.edit', [$patient, $vaccine]) }}" class="text-purple-600 text-sm">Editar</a>
                            <form method="post" action="{{ route('patients.immunizations.destroy', [$patient, $vaccine]) }}" onsubmit="return confirm('¿Eliminar vacuna?')">
                                @csrf
                                @method('delete')
                                <button class="text-rose-600 text-sm">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No hay vacunas registradas.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="glass-card rounded-2xl p-5 shadow">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Desparasitación interna</p>
                        <h3 class="text-lg font-semibold text-slate-900">Control digestivo</h3>
                    </div>
                    <a href="{{ route('patients.dewormings.create', [$patient, 'internal']) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600">Registrar interna</a>
                </div>
                <div class="space-y-3">
                    @forelse($internalDewormings as $item)
                        @php
                            $statusColor = match(true) {
                                $item->status === 'overdue' => 'bg-red-100 text-red-700',
                                $item->status === 'scheduled' => 'bg-amber-100 text-amber-700',
                                default => 'bg-emerald-100 text-emerald-700',
                            };
                        @endphp
                        <div class="p-4 rounded-xl border border-slate-100 flex justify-between items-start">
                            <div class="space-y-1">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">{{ ucfirst($item->status) }}</span>
                                <p class="text-lg font-semibold text-slate-900">{{ $item->item->nombre ?? $item->item_manual }}</p>
                                <p class="text-xs text-slate-500">Dosis: {{ $item->dose ?? 'N/D' }} · Ruta: {{ $item->route ?? 'N/D' }}</p>
                                @if($item->next_due_at)
                                    <p class="text-xs text-slate-500">Próxima: {{ optional($item->next_due_at)->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col gap-2 text-right">
                                <a href="{{ route('patients.dewormings.edit', [$patient, $item]) }}" class="text-purple-600 text-sm">Editar</a>
                                <form method="post" action="{{ route('patients.dewormings.destroy', [$patient, $item]) }}" onsubmit="return confirm('¿Eliminar desparasitación?')">
                                    @csrf
                                    @method('delete')
                                    <button class="text-rose-600 text-sm">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500">Aún no hay registros internos.</p>
                    @endforelse
                </div>
            </div>

            <div class="glass-card rounded-2xl p-5 shadow">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Desparasitación externa</p>
                        <h3 class="text-lg font-semibold text-slate-900">Piel y protección</h3>
                    </div>
                    <a href="{{ route('patients.dewormings.create', [$patient, 'external']) }}" class="px-4 py-2 rounded-full text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600">Registrar externa</a>
                </div>
                <div class="space-y-3">
                    @forelse($externalDewormings as $item)
                        @php
                            $statusColor = match(true) {
                                $item->status === 'overdue' => 'bg-red-100 text-red-700',
                                $item->status === 'scheduled' => 'bg-amber-100 text-amber-700',
                                default => 'bg-emerald-100 text-emerald-700',
                            };
                        @endphp
                        <div class="p-4 rounded-xl border border-slate-100 flex justify-between items-start">
                            <div class="space-y-1">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">{{ ucfirst($item->status) }}</span>
                                <p class="text-lg font-semibold text-slate-900">{{ $item->item->nombre ?? $item->item_manual }}</p>
                                <p class="text-xs text-slate-500">Dosis: {{ $item->dose ?? 'N/D' }} · Ruta: {{ $item->route ?? 'N/D' }}</p>
                                @if($item->duration_days)
                                    <p class="text-xs text-slate-500">Duración: {{ $item->duration_days }} días</p>
                                @endif
                                @if($item->next_due_at)
                                    <p class="text-xs text-slate-500">Próxima: {{ optional($item->next_due_at)->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col gap-2 text-right">
                                <a href="{{ route('patients.dewormings.edit', [$patient, $item]) }}" class="text-purple-600 text-sm">Editar</a>
                                <form method="post" action="{{ route('patients.dewormings.destroy', [$patient, $item]) }}" onsubmit="return confirm('¿Eliminar desparasitación?')">
                                    @csrf
                                    @method('delete')
                                    <button class="text-rose-600 text-sm">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500">Aún no hay registros externos.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
