@extends('layouts.vertical', ['subtitle' => 'Pacientes'])

@section('content')
    @include('layouts.partials/page-title', ['title' => 'Reenganche', 'subtitle' => 'Pacientes'])

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Selecciona un paciente para comunicarte por WhatsApp y llenar huecos disponibles.</p>
            <div class="list-group list-group-flush">
                @forelse ($pacientes as $paciente)
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="https://wa.me/{{ preg_replace('/\D+/', '', $paciente->whatsapp) }}" target="_blank">
                        <span>{{ $paciente->nombres }} {{ $paciente->apellidos }}</span>
                        <span class="text-muted">{{ $paciente->whatsapp }}</span>
                    </a>
                @empty
                    <div class="list-group-item text-center text-muted">No hay pacientes para contactar.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
