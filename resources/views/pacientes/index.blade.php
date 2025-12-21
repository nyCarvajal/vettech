@extends('layouts.vertical', ['subtitle' => 'Pacientes'])

@section('content')
    @include('layouts.partials/page-title', ['title' => 'Pacientes', 'subtitle' => 'Listado'])

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary">Nuevo paciente</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Ciudad</th>
                            <th>WhatsApp</th>
                            <th>Correo</th>
                            <th>Fecha de nacimiento</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pacientes as $paciente)
                            <tr>
                                <td>{{ $paciente->id }}</td>
                                <td>{{ trim(($paciente->tipo_documento ?? '') . ' ' . ($paciente->numero_documento ?? '')) ?: '—' }}</td>
                                <td>{{ $paciente->nombres }}</td>
                                <td>{{ $paciente->apellidos }}</td>
                                <td>{{ $paciente->ciudad ?? '—' }}</td>
                                <td>{{ $paciente->whatsapp }}</td>
                                <td>{{ $paciente->email ?? '—' }}</td>
                                <td>{{ $paciente->fecha_nacimiento ? \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->format('Y-m-d') : '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                    <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay pacientes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pacientes->links() }}
            </div>
        </div>
    </div>
@endsection
