@extends('layouts.vertical', ['subtitle' => 'Citas pendientes'])

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Citas pendientes</h4>
            <p class="text-muted mb-0">Confirma o reprograma las solicitudes en espera.</p>
        </div>
        <a href="{{ route('reservas.calendar') }}" class="btn btn-light">Volver al calendario</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                        <tr>
                            <td>{{ $reserva->paciente?->nombres ?? 'Paciente' }}</td>
                            <td>{{ $reserva->entrenador?->nombres ?? 'Sin asignar' }}</td>
                            <td>{{ optional($reserva->fecha)->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $reserva->estado }}</span></td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('reservas.pending.confirm', $reserva) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Confirmar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay citas pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
