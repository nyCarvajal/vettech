@extends('layouts.vertical')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Clínicas</h4>
        <a href="{{ route('clinicas.create') }}" class="btn btn-primary">Nueva clínica</a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($clinicas as $clinica)
                    <tr>
                        <td>{{ $clinica->nombre }}</td>
                        <td>{{ $clinica->email ?? '—' }}</td>
                        <td>{{ $clinica->telefono ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('clinicas.edit', $clinica) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">No hay clínicas registradas aún.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($clinicas->hasPages())
            <div class="card-footer">
                {{ $clinicas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
