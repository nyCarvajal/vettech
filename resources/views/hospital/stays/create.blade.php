@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Admitir paciente</h1>
    <form method="post" action="{{ route('hospital.stays.store') }}" class="space-y-4">
        @csrf

        @if($patient)
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            @if($patient->owner)
                <input type="hidden" name="owner_id" value="{{ $patient->owner->id }}">
            @endif
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Paciente</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $patient->name }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-mint-50 text-mint-700">{{ ucfirst($patient->species->name ?? 'Desconocido') }}</span>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div>
                        <dt class="text-gray-500">Propietario</dt>
                        <dd class="font-medium">{{ $patient->owner->name ?? 'Sin tutor' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Identificador</dt>
                        <dd class="font-medium">#{{ $patient->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Edad</dt>
                        <dd class="font-medium">{{ $patient->age ?? 'N/D' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Notas</dt>
                        <dd class="font-medium">{{ $patient->notes ?? 'Sin notas' }}</dd>
                    </div>
                </dl>
            </div>
        @else
            <label class="block text-sm font-medium text-gray-700" for="patient_id">Paciente ID</label>
            <input
                id="patient_id"
                name="patient_id"
                placeholder="Paciente ID"
                value="{{ old('patient_id', request('patient_id')) }}"
                class="border p-2 w-full rounded-lg"
                required
            >
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="cage_id">Jaula</label>
                <select name="cage_id" id="cage_id" class="border p-2 w-full rounded-lg">
                    @foreach($cages as $cage)
                        <option value="{{ $cage->id }}">{{ $cage->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700" for="admitted_at">Fecha y hora de ingreso</label>
                <input
                    type="datetime-local"
                    name="admitted_at"
                    id="admitted_at"
                    class="border p-2 w-full rounded-lg"
                    value="{{ old('admitted_at', now()->format('Y-m-d\TH:i')) }}"
                    required
                >
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="severity">Severidad</label>
                <select name="severity" id="severity" class="border p-2 w-full rounded-lg">
                    <option value="stable" @selected(old('severity') === 'stable')>Estable</option>
                    <option value="observation" @selected(old('severity') === 'observation')>Observación</option>
                    <option value="critical" @selected(old('severity') === 'critical')>Crítico</option>
                </select>
            </div>
            <div class="space-y-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="plan">Plan</label>
                <textarea name="plan" id="plan" class="border p-2 w-full rounded-lg" placeholder="Plan">{{ old('plan') }}</textarea>
            </div>
            <div class="space-y-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700" for="diet">Dieta</label>
                <textarea name="diet" id="diet" class="border p-2 w-full rounded-lg" placeholder="Dieta">{{ old('diet') }}</textarea>
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700" for="primary_dx">Diagnóstico</label>
            <textarea name="primary_dx" id="primary_dx" class="border p-2 w-full rounded-lg" placeholder="Diagnóstico">{{ old('primary_dx') }}</textarea>
        </div>

        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
