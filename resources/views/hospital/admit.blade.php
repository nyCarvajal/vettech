@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-card class="bg-white border border-emerald-100">
        <h1 class="text-xl font-semibold text-emerald-700 mb-4">Admitir paciente</h1>
        <form method="POST" action="{{ route('hospital.store') }}" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <x-input label="Paciente" name="patient_id" value="{{ old('patient_id', $patient->id ?? '') }}" />
                <x-input label="Tutor" name="owner_id" value="{{ old('owner_id', $patient->owner_id ?? '') }}" />
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <x-input label="Fecha de admisión" type="datetime-local" name="admitted_at" />
                <x-input label="Jaula" name="cage_id" />
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <x-select label="Severidad" name="severity" :options="['stable'=>'Estable','observation'=>'Observación','critical'=>'Crítica']" />
                <x-input label="Creado por (user id)" name="created_by" />
            </div>
            <x-textarea label="Diagnóstico principal" name="primary_dx" />
            <x-textarea label="Plan" name="plan" />
            <x-textarea label="Dieta" name="diet" />
            <div class="flex justify-end space-x-2">
                <x-button href="{{ route('hospital.index') }}" color="secondary">Cancelar</x-button>
                <x-button type="submit">Admitir</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
