@extends('layouts.app', ['subtitle' => 'Razas'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Razas</h1>
        <a href="{{ route('razas.create') }}" class="btn btn-primary">Nueva raza</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Nombre</th>
                            <th>Especie</th>
                            <th class="text-end" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($breeds as $raza)
                            <tr>
                                <td class="text-center">{{ $raza->id }}</td>
                                <td>{{ $raza->name }}</td>
                                <td>{{ $raza->species?->name ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('razas.edit', $raza) }}" class="btn btn-sm btn-primary">Editar</a>
                                    <form action="{{ route('razas.destroy', $raza) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta raza?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No hay razas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($breeds->hasPages())
            <div class="card-footer">
                {{ $breeds->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
