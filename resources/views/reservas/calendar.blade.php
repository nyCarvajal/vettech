@extends('layouts.vertical', ['subtitle' => 'Calendario de citas'])

@section('content')
<div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Agenda médica</h4>
            <p class="text-muted mb-0">Visualiza las citas por profesional y agenda nuevas en un clic.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reservationModal">
            <i class="bx bx-plus"></i> Nueva cita
        </button>
    </div>

    @if(session('status'))
        <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="medicoFilter" class="form-label mb-1">Filtrar por médico</label>
                    <select id="medicoFilter" class="form-select">
                        <option value="">Todos</option>
                        @foreach($medicos as $medico)
                            <option value="{{ $medico->id }}">{{ $medico->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="agendaDate" class="form-label mb-1">Fecha</label>
                    <input type="date" id="agendaDate" name="fecha" value="{{ $selectedDate->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-success me-2">Confirmada</span>
                    <span class="badge bg-warning text-dark me-2">Pendiente</span>
                    <span class="badge bg-danger">Cancelada</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div id="calendar" class="fullcalendar-container" style="min-height: 750px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-3">
                            <div class="fw-semibold">Citas hoy</div>
                            <div class="h4 mb-0" id="statTotal">{{ $stats['total'] }}</div>
                        </div>
                        <div class="badge bg-success-subtle text-success px-3 py-2 rounded-3">
                            <div class="fw-semibold">Atendidas</div>
                            <div class="h4 mb-0" id="statAtendidas">{{ $stats['atendidas'] }}</div>
                        </div>
                        <div class="badge bg-warning-subtle text-warning px-3 py-2 rounded-3">
                            <div class="fw-semibold">Pendientes</div>
                            <div class="h4 mb-0" id="statPendientes">{{ $stats['pendientes'] }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="next-3" id="filterNext3">
                            <label class="form-check-label" for="filterNext3">
                                Próximas 3 horas
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visitaFilter" id="filterPrimera" value="Primera visita">
                            <label class="form-check-label" for="filterPrimera">Solo primeras visitas</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visitaFilter" id="filterControl" value="Control">
                            <label class="form-check-label" for="filterControl">Solo controles</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visitaFilter" id="filterAny" value="" checked>
                            <label class="form-check-label" for="filterAny">Todas</label>
                        </div>
                    </div>

                    <div class="table-responsive border rounded">
                        <table class="table table-sm align-middle mb-0" id="agendaTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Motivo</th>
                                    <th>Modalidad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agenda as $reserva)
                                    <tr data-hora="{{ optional($reserva->fecha)->format('H:i') }}" data-visita="{{ $reserva->visita_tipo }}" data-estado="{{ $reserva->estado }}">
                                        <td class="text-nowrap fw-semibold">{{ optional($reserva->fecha)->format('H:i') }}</td>
                                        <td>{{ $reserva->paciente?->nombres ?? 'Paciente' }}</td>
                                        <td>{{ $reserva->tipocita?->nombre ?? ($reserva->nota_cliente ?? 'Motivo no indicado') }}</td>
                                        <td><span class="badge bg-info-subtle text-info">{{ $reserva->modalidad ?? 'Presencial' }}</span></td>
                                        <td><span class="badge" data-status-badge>{{ $reserva->estado }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Sin citas para la fecha seleccionada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="reservationModalLabel">Gestionar cita</h5>
                    <p class="text-muted mb-0">Define paciente, tipo de consulta, médico y horario disponible.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="calendarForm" method="POST" action="{{ route('reservas.store') }}" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" id="reservationMethod" name="_method" value="POST">
                <input type="hidden" id="start" name="start">
                <input type="hidden" id="eventId">

                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Paciente</label>
                            <select class="form-select" id="pacienteId" name="paciente_id" required>
                                <option value="">Selecciona un paciente</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->nombres }} {{ $paciente->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="fieldEntrenador">
                            <label class="form-label">Médico</label>
                            <select class="form-select" id="entrenador" name="entrenador_id" required>
                                <option value="">Selecciona un médico</option>
                                @foreach($medicos as $medico)
                                    <option value="{{ $medico->id }}">{{ $medico->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="fieldServicio">
                            <label class="form-label">Tipo de consulta</label>
                            <select class="form-select" id="servicio" name="tipocita_id" required>
                                <option value="">Elige el tipo de consulta</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="reservaFecha" name="fecha" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hora disponible</label>
                            <select class="form-select" id="reservaHora" name="hora" required>
                                <option value="">-- Elige hora --</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Duración</label>
                            <select class="form-select" id="reservaDuracion" name="duracion">
                                <option value="30">30 min</option>
                                <option value="45">45 min</option>
                                <option value="60" selected>60 min</option>
                                <option value="90">90 min</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="reservaEstado" name="estado">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Confirmada">Confirmada</option>
                                <option value="Cancelada">Cancelada</option>
                                <option value="En curso">En curso</option>
                                <option value="Finalizada">Finalizada</option>
                                <option value="No Asistió">No asistió</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Modalidad</label>
                            <select class="form-select" id="reservaModalidad" name="modalidad">
                                <option value="Presencial">Presencial</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo de visita</label>
                            <select class="form-select" id="reservaVisita" name="visita_tipo">
                                <option value="Primera visita">Primera visita</option>
                                <option value="Control" selected>Control</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Motivo de consulta</label>
                            <textarea class="form-control" name="nota_cliente" rows="2" placeholder="Motivo, síntomas o indicaciones adicionales"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger d-none" id="reservationCancel">
                        <span data-cancel-label>Cancelar cita</span>
                    </button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.CalendarConfig = {
        selector: '#calendar',
        modalSelector: '#reservationModal',
        eventsUrl: '{{ route('reservas.events') }}',
        filterSelector: '#medicoFilter'
    };

    const badgeColors = {
        'Confirmada': 'bg-success',
        'Pendiente': 'bg-warning text-dark',
        'Cancelada': 'bg-danger',
        'En curso': 'bg-primary',
        'Finalizada': 'bg-success',
        'No Asistió': 'bg-secondary',
    };

    const dateInput = document.querySelector('#agendaDate');
    const rows = Array.from(document.querySelectorAll('#agendaTable tbody tr'));
    const statTotal = document.querySelector('#statTotal');
    const statAtendidas = document.querySelector('#statAtendidas');
    const statPendientes = document.querySelector('#statPendientes');
    const next3Toggle = document.querySelector('#filterNext3');
    const visitaRadios = Array.from(document.querySelectorAll('input[name="visitaFilter"]'));

    const applyStatusBadges = () => {
        rows.forEach(row => {
            const badge = row.querySelector('[data-status-badge]');
            if (!badge) return;
            badge.className = 'badge';
            const estado = badge.textContent?.trim();
            if (estado && badgeColors[estado]) {
                badge.classList.add(...badgeColors[estado].split(' '));
            }
        });
    };

    const updateStats = (visibleRows) => {
        const total = visibleRows.length;
        const pendientes = visibleRows.filter(r => r.dataset.estado === 'Pendiente').length;
        const atendidas = visibleRows.filter(r => ['En curso', 'Finalizada', 'No Asistió'].includes(r.dataset.estado)).length;

        if (statTotal) statTotal.textContent = total;
        if (statPendientes) statPendientes.textContent = pendientes;
        if (statAtendidas) statAtendidas.textContent = atendidas;
    };

    const filterAgenda = () => {
        const selectedVisita = visitaRadios.find(r => r.checked)?.value || '';
        const now = new Date();
        const selectedDate = new Date(dateInput?.value || now.toISOString().slice(0, 10));
        const windowEnd = new Date(now.getTime() + (3 * 60 * 60 * 1000));

        const visibleRows = rows.filter(row => {
            let visible = true;
            if (selectedVisita) {
                visible = visible && row.dataset.visita === selectedVisita;
            }

            if (next3Toggle?.checked) {
                const [hour, minute] = (row.dataset.hora || '00:00').split(':').map(Number);
                const rowDate = new Date(selectedDate);
                rowDate.setHours(hour, minute, 0, 0);
                visible = visible && rowDate >= now && rowDate <= windowEnd;
            }

            row.classList.toggle('d-none', !visible);
            return visible;
        });

        updateStats(visibleRows);
    };

    applyStatusBadges();
    filterAgenda();

    next3Toggle?.addEventListener('change', filterAgenda);
    visitaRadios.forEach(radio => radio.addEventListener('change', filterAgenda));

    dateInput?.addEventListener('change', () => {
        const queryDate = dateInput.value;
        if (queryDate) {
            const url = new URL(window.location.href);
            url.searchParams.set('fecha', queryDate);
            window.location.href = url.toString();
        }
    });
</script>
@endpush
@endsection
