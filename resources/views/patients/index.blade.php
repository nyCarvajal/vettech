@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Pacientes</h1>
            <p class="text-muted mb-0">Explora pacientes por especie, raza y tutor.</p>
        </div>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">Nuevo paciente</a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar paciente">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Especie</label>
                    <select name="species_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($species as $item)
                            <option value="{{ $item->id }}" @selected(request('species_id') == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Raza</label>
                    <select name="breed_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}" @selected(request('breed_id') == $breed->id)>{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tutor</label>
                    <select name="owner_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" @selected(request('owner_id') == $owner->id)>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-outline-secondary" type="submit">Filtrar</button>
                    <a href="{{ route('patients.index') }}" class="btn btn-link">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th>Especie / Raza</th>
                        <th>Tutor</th>
                        <th>Peso</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td class="fw-semibold">{{ $patient->display_name }}</td>
                        <td class="text-muted">{{ optional($patient->species)->name }} · {{ optional($patient->breed)->name }}</td>
                        <td>{{ optional($patient->owner)->name }}</td>
                        <td>{{ $patient->peso_formateado ?? 'N/D' }}</td>
                        <td class="text-end"><a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">Ver perfil</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay pacientes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $patients->links() }}</div>
    </div>
</div>
@endsection
