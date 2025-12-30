@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-slate-500">{{ $immunization->exists ? 'Editar' : 'Registrar' }} vacuna</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $patient->display_name }}</h1>
        </div>
        <a href="{{ route('patients.carnet', $patient) }}" class="px-4 py-2 rounded-full bg-white border text-slate-700">Volver al carnet</a>
    </div>

    <form method="post" action="{{ $immunization->exists ? route('patients.immunizations.update', [$patient, $immunization]) : route('patients.immunizations.store', $patient) }}" class="space-y-4 p-6 rounded-2xl bg-white shadow" novalidate>
        @csrf
        @if($immunization->exists)
            @method('put')
        @endif

        <input type="hidden" name="paciente_id" value="{{ $patient->id }}">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Fecha de aplicación</label>
                <input type="date" name="applied_at" value="{{ old('applied_at', optional($immunization->applied_at)->toDateString()) }}" class="w-full rounded-xl border-slate-200">
                @error('applied_at')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Próxima dosis</label>
                <input type="date" name="next_due_at" value="{{ old('next_due_at', optional($immunization->next_due_at)->toDateString()) }}" class="w-full rounded-xl border-slate-200">
                @error('next_due_at')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Vacuna</label>
                <input type="text" name="vaccine_name" value="{{ old('vaccine_name', $immunization->vaccine_name) }}" placeholder="Rabia, Moquillo..." class="w-full rounded-xl border-slate-200">
                @error('vaccine_name')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 mt-6">
                <input type="hidden" name="contains_rabies" value="0">
                <input type="checkbox" name="contains_rabies" value="1" {{ old('contains_rabies', $immunization->contains_rabies) ? 'checked' : '' }} class="rounded">
                <span class="text-sm text-rose-600 font-semibold">Contiene rabia</span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Producto del inventario</label>
                <select name="item_id" class="w-full rounded-xl border-slate-200">
                    <option value="">-- Seleccionar item --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id', $immunization->item_id)==$item->id)>{{ $item->nombre }}</option>
                    @endforeach
                </select>
                @error('item_id')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">O escribir manual</label>
                <input type="text" name="item_manual" value="{{ old('item_manual', $immunization->item_manual) }}" placeholder="Nombre comercial" class="w-full rounded-xl border-slate-200">
                @error('item_manual')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Lote</label>
                <input type="text" name="batch_lot" value="{{ old('batch_lot', $immunization->batch_lot) }}" class="w-full rounded-xl border-slate-200" required>
                @error('batch_lot')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Dosis</label>
                <input type="text" name="dose" value="{{ old('dose', $immunization->dose) }}" class="w-full rounded-xl border-slate-200">
                @error('dose')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Estado</label>
                <select name="status" class="w-full rounded-xl border-slate-200">
                    @foreach(['applied' => 'Aplicada', 'scheduled' => 'Programada', 'overdue' => 'Vencida'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $immunization->status)==$value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-rose-600 text-sm">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Notas</label>
            <textarea name="notes" rows="3" class="w-full rounded-xl border-slate-200">{{ old('notes', $immunization->notes) }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('patients.carnet', $patient) }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700">Cancelar</a>
            <button class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-purple-500 to-emerald-400 shadow">Guardar</button>
        </div>
    </form>
</div>
@endsection
