@extends('layouts.vertical', ['subtitle' => 'Pacientes'])

@section('content')
    @include('layouts.partials/page-title', ['title' => 'Cumpleaños', 'subtitle' => 'Pacientes'])

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Cumpleaños de este mes</h5>
            <ul class="list-group list-group-flush">
                @forelse ($cumpleanos as $paciente)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $paciente->nombres }} {{ $paciente->apellidos }}</span>
                        <span class="badge bg-primary">{{ \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->format('d/m') }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">No hay cumpleaños pendientes este mes.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
