@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Admitir paciente</h1>
    <form method="post" action="{{ route('hospital.stays.store') }}" class="space-y-2">
        @csrf
        <input
            name="patient_id"
            placeholder="Paciente ID"
            value="{{ old('patient_id', request('patient_id')) }}"
            class="border p-2 w-full"
            required
        >
        <select name="cage_id" class="border p-2 w-full">
            @foreach($cages as $cage)
                <option value="{{ $cage->id }}">{{ $cage->name }}</option>
            @endforeach
        </select>
        <input type="datetime-local" name="admitted_at" class="border p-2 w-full" required>
        <select name="severity" class="border p-2 w-full">
            <option value="stable">Estable</option>
            <option value="observation">Observación</option>
            <option value="critical">Crítico</option>
        </select>
        <textarea name="diagnosis" class="border p-2 w-full" placeholder="Diagnóstico"></textarea>
        <textarea name="plan" class="border p-2 w-full" placeholder="Plan"></textarea>
        <textarea name="diet" class="border p-2 w-full" placeholder="Dieta"></textarea>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
