@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tutores</h1>
            <p class="text-muted mb-0">Administra los datos de los tutores y sus mascotas.</p>
        </div>
        <a href="{{ route('owners.create') }}" class="btn btn-primary">Nuevo tutor</a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nombre, teléfono o documento">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Mascotas</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($owners as $owner)
                    <tr>
                        <td class="fw-semibold">{{ $owner->name }}</td>
                        <td class="text-muted small">
                            <div>{{ $owner->phone }}</div>
                            <div>{{ $owner->email }}</div>
                        </td>
                        <td><span class="badge bg-soft-primary text-primary">{{ $owner->patients_count }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('owners.show', $owner) }}" class="btn btn-sm btn-outline-primary">Ver perfil</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No hay tutores registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $owners->links() }}</div>
    </div>
</div>
@endsection
