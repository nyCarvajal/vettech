@extends('layouts.app', ['subtitle' => 'Tipos de cita'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tipos de cita</h1>
        <a href="{{ route('tipocitas.create') }}" class="btn btn-primary">Nuevo tipo</a>
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
                            <th>Descripción</th>
                            <th class="text-center" style="width: 140px;">Duración</th>
                            <th class="text-end" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipocitas as $tipocita)
                            <tr>
                                <td class="text-center">{{ $tipocita->id }}</td>
                                <td>{{ $tipocita->nombre }}</td>
                                <td>{{ $tipocita->descripcion ?: '—' }}</td>
                                <td class="text-center">{{ $tipocita->duracion ? $tipocita->duracion . ' min' : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tipocitas.edit', $tipocita) }}" class="btn btn-sm btn-primary">Editar</a>
                                    <form action="{{ route('tipocitas.destroy', $tipocita) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este tipo de cita?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No hay tipos de cita registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($tipocitas->hasPages())
            <div class="card-footer">
                {{ $tipocitas->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
