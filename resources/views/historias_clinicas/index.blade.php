@extends('layouts.app', ['subtitle' => 'Historia clínica'])

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Historias clínicas</h1>
        <a href="{{ route('historias-clinicas.create') }}" class="btn btn-primary">Nueva historia</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Estado</th>
                        <th>Última actualización</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historias as $historia)
                        <tr>
                            <td>
                                {{ $historia->paciente->nombres ?? 'Paciente' }} {{ $historia->paciente->apellidos ?? '' }}<br>
                                <small class="text-muted">ID: {{ $historia->paciente_id }}</small>
                            </td>
                            <td><span class="badge {{ $historia->estado === 'definitiva' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($historia->estado) }}</span></td>
                            <td>{{ optional($historia->updated_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('historias-clinicas.edit', $historia) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form action="{{ route('historias-clinicas.destroy', $historia) }}" method="POST" onsubmit="return confirm('¿Eliminar esta historia clínica?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Aún no hay historias clínicas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $historias->links() }}
    </div>
</div>
@endsection
