@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-slate-500">{{ $deworming->exists ? 'Editar' : 'Registrar' }} desparasitación {{ $type === 'internal' ? 'interna' : 'externa' }}</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $patient->display_name }}</h1>
        </div>
        <a href="{{ route('patients.carnet', $patient) }}" class="px-4 py-2 rounded-full bg-white border text-slate-700">Volver al carnet</a>
    </div>

    <form method="post" action="{{ $deworming->exists ? route('patients.dewormings.update', [$patient, $deworming]) : route('patients.dewormings.store', $patient) }}" class="space-y-4 p-6 rounded-2xl bg-white shadow" novalidate>
        @csrf
        @if($deworming->exists)
            @method('put')
        @endif

        <input type="hidden" name="paciente_id" value="{{ $patient->id }}">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Fecha de aplicación</label>
                <input type="date" name="applied_at" value="{{ old('applied_at', optional($deworming->applied_at)->toDateString()) }}" class="w-full rounded-xl border border-slate-200">
                @error('applied_at')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Próxima dosis</label>
                <input type="date" name="next_due_at" value="{{ old('next_due_at', optional($deworming->next_due_at)->toDateString()) }}" class="w-full rounded-xl border border-slate-200">
                @error('next_due_at')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Producto del inventario</label>
                <select name="item_id" class="w-full rounded-xl border border-slate-200">
                    <option value="">-- Seleccionar item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id', $deworming->item_id)==$item->id)>{{ $item->nombre }}</option>
                    @endforeach
                </select>
                @error('item_id')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">O escribir manual</label>
                <input type="text" name="item_manual" value="{{ old('item_manual', $deworming->item_manual) }}" placeholder="Nombre comercial" class="w-full rounded-xl border border-slate-200">
                @error('item_manual')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Dosis</label>
                <input type="text" name="dose" value="{{ old('dose', $deworming->dose) }}" class="w-full rounded-xl border border-slate-200">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Ruta</label>
                <input type="text" name="route" value="{{ old('route', $deworming->route) }}" class="w-full rounded-xl border border-slate-200">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Duración (días)</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', $deworming->duration_days) }}" class="w-full rounded-xl border border-slate-200">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Estado</label>
                <select name="status" class="w-full rounded-xl border border-slate-200">
                    @foreach(['applied' => 'Aplicada', 'scheduled' => 'Programada', 'overdue' => 'Vencida'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $deworming->status)==$value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Notas</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-200">{{ old('notes', $deworming->notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('patients.carnet', $patient) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700">Cancelar</a>
            <button class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-purple-500 to-emerald-400 shadow">Guardar</button>
        </div>
    </form>
</div>
@endsection
