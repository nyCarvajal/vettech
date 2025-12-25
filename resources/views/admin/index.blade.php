@extends('layouts.app', ['subtitle' => 'Inicio'])

@section('content')

@include('layouts.partials/page-title', ['title' => 'Panel de control', 'subtitle' => 'Inicio'])

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4" style="background-color: #ffffff; border: 2px solid #c8a2c8;">
            <div class="card-body d-flex flex-wrap align-items-start gap-4">
                <div class="flex-grow-1">
                    <p class="text-uppercase text-muted fw-semibold mb-1">Hoy</p>
                    <h3 class="fw-semibold text-dark mb-3">{{ ucfirst($fechaHoyLegible) }}</h3>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="text-muted d-block">Asistencia</span>
                            <span class="fw-semibold fs-5 text-dark">{{ $asistenciaPorcentaje }}%</span>
                            <small class="text-success d-block">
                                ({{ $ausenciasRecuperadas }} ausencia{{ $ausenciasRecuperadas === 1 ? '' : 's' }} recuperada{{ $ausenciasRecuperadas === 1 ? '' : 's' }} con recordatorio WhatsApp ✅)
                            </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Citas confirmadas hoy</span>
                            <span class="fw-semibold fs-6 text-dark">{{ $confirmadasHoy }} / {{ $totalAgendadasHoy }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Huecos libres</span>
                            <span class="fw-semibold fs-6 text-dark">{{ $totalHuecosDisponibles }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-muted text-uppercase fw-semibold mb-1">Clientes registrados</p>
                    <h3 class="mb-0">{{ number_format($totalClientes) }}</h3>
                </div>
                <div class="ms-3 avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:users-group-two-rounded-broken" class="fs-32 text-primary"></iconify-icon>
                </div>
            </div>
            <div id="chart02"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-muted text-uppercase fw-semibold mb-1">Citas del mes</p>
                    <h3 class="mb-0">{{ $totalReservas }}</h3>
                </div>
                <div class="ms-3 avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:calendar-outline" class="fs-32 text-primary"></iconify-icon>
                </div>
            </div>
            <div id="chart03"></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-muted text-uppercase fw-semibold mb-1">Huecos libres hoy</p>
                    <h3 class="mb-0">{{ $totalHuecosDisponibles }}</h3>
                </div>
                <div class="ms-3 avatar-md bg-soft-primary rounded d-flex align-items-center justify-content-center">
                    <iconify-icon icon="solar:clock-circle-linear" class="fs-32 text-primary"></iconify-icon>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">Agenda de hoy</h4>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-4 d-flex flex-column gap-2">
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">📅</span>
                        <span>Citas para hoy:</span>
                        <span class="ms-auto fw-semibold text-dark">{{ $confirmadasHoy }} / {{ $totalAgendadasHoy }} confirmadas</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">⏳</span>
                        <span>Huecos libres esta tarde:</span>
                        <span class="ms-auto fw-semibold text-dark">{{ $totalHuecosDisponibles }}</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="fs-4">🚫</span>
                        <span>Ausencias:</span>
                        <span class="ms-auto fw-semibold text-dark">{{ $ausenciasHoy }}</span>
                    </li>
                </ul>
                <a href="{{ route('clientes.reengage') }}" class="btn btn-success btn-lg w-100">
                    Llenar huecos por WhatsApp
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">Huecos Libres de Hoy</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Quedan {{ $totalHuecosDisponibles }} espacio{{ $totalHuecosDisponibles == 1 ? '' : 's' }} libre{{ $totalHuecosDisponibles == 1 ? '' : 's' }}:</p>
                <div class="list-group list-group-flush">
                    @forelse ($huecosDestacados as $hueco)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $hueco->inicio->format('H:i') }}</h5>
                                <p class="text-muted mb-0">{{ $hueco->servicio }} ({{ $hueco->barbero }})</p>
                            </div>
                            <span class="badge bg-soft-primary text-primary">{{ $hueco->duracion }} min</span>
                        </div>
                    @empty
                        <div class="text-muted py-4 text-center">
                            Agenda completa por ahora.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-light border-0">
                <a href="{{ route('clientes.reengage') }}" class="btn btn-outline-success w-100">
                    Compartir huecos por WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Nuevos Clientes</h4>
                <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-light">Ver todos</a>
            </div>
            <div class="card-body pb-1">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-centered">
                        <thead>
                            <tr>
                                <th class="py-1">ID</th>
                                <th class="py-1">Nombres</th>
                                <th class="py-1">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('clientes.show', $cliente) }}">{{ $cliente->nombres }} {{ $cliente->apellidos }}</a></td>
                                    <td>
                                        @php
                                            $clean = preg_replace('/\D+/', '', $cliente->whatsapp);
                                        @endphp
                                        <a href="https://wa.me/{{ $clean }}" target="_blank">{{ $cliente->whatsapp }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aún no hay clientes</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
