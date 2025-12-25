@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Remisión de exámenes - Historia #{{ $historia->id }}</h1>
    <form method="post" action="{{ route('historias-clinicas.remisiones.store', $historia) }}">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Médico remitente</label>
                    <input type="text" name="doctor_name" class="form-control" value="{{ old('doctor_name') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Exámenes solicitados</label>
                    <textarea name="tests" class="form-control" rows="4" required>{{ old('tests') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notas adicionales</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('historias-clinicas.show', $historia) }}" class="btn btn-link">Cancelar</a>
            <button type="submit" class="btn btn-info text-white">Guardar remisión</button>
        </div>
    </form>
</div>
@endsection
