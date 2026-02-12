<!DOCTYPE html>
<html lang="es">
<head>
@php
    $trainerLabelSingular = $trainerLabelSingular ?? \App\Models\Clinica::defaultRoleLabel(\App\Models\Clinica::ROLE_STYLIST);
    $trainerLabelPlural = $trainerLabelPlural ?? \App\Models\Clinica::defaultRoleLabel(\App\Models\Clinica::ROLE_STYLIST, true);
    $contactPhone = $clinica->telefono ?? $clinica->phone ?? $clinica->whatsapp ?? null;
    $contactEmail = $clinica->correo ?? $clinica->email ?? null;
    $contactAddress = $clinica->direccion ?? $clinica->address ?? null;
    $ownerName = trim((string) ($cliente->name ?? ''));
    $ownerFirstName = $ownerName !== '' ? \Illuminate\Support\Str::before($ownerName, ' ') : '';
    $ownerLastName = $ownerName !== '' ? trim(\Illuminate\Support\Str::after($ownerName, ' ')) : '';
@endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda tu cita - {{ $clinica->nombre }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --mint-100: #e0fbf7;
            --mint-300: #8debe0;
            --mint-500: #44d7c5;
            --purple-100: #efeaff;
            --purple-300: #cbb7ff;
            --purple-500: #7b6df1;
            --ink-900: #1b1f2a;
            --ink-600: #5c6273;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top, #ffffff 0%, #f5f7fb 50%, #eef0f7 100%);
            color: var(--ink-900);
        }
        .clinic-shell {
            max-width: 1200px;
        }
        .hero {
            background: linear-gradient(135deg, var(--purple-300) 0%, var(--mint-300) 100%);
            border-radius: 28px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.45), transparent 60%);
            opacity: 0.8;
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-logo {
            width: 110px;
            height: 110px;
            border-radius: 26px;
            background: rgba(255, 255, 255, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem;
            box-shadow: 0 10px 25px rgba(123, 109, 241, 0.25);
        }
        .hero-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .badge-soft {
            background: rgba(255, 255, 255, 0.55);
            color: var(--ink-600);
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .card-soft {
            background: #ffffff;
            border-radius: 22px;
            border: none;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        }
        .section-title {
            font-weight: 700;
            font-size: 1.1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--purple-500), var(--mint-500));
            border: none;
            box-shadow: 0 10px 20px rgba(123, 109, 241, 0.25);
        }
        .btn-primary:hover {
            filter: brightness(0.95);
        }
        .contact-item {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .contact-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--mint-500);
            margin-top: 0.35rem;
        }
        .timeline-item {
            position: relative;
            padding-left: 1.75rem;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.6rem;
            top: 0.2rem;
            width: 10px;
            height: 10px;
            background: var(--purple-500);
            border-radius: 50%;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 1rem;
            top: 1.2rem;
            bottom: -1.2rem;
            width: 2px;
            background: rgba(15, 23, 42, 0.1);
        }
        .timeline-item:last-child::after {
            display: none;
        }
        @media (max-width: 992px) {
            .hero {
                padding: 2rem;
            }
            .hero-logo {
                width: 90px;
                height: 90px;
            }
        }
    </style>
</head>
<body>
<div class="container clinic-shell py-5">
    <div class="hero mb-5">
        <div class="hero-content">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <div class="hero-logo">
                    <img src="{{ $clinicaLogo }}" alt="Logo de {{ $clinica->nombre }}">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge-soft">Atención con amor y precisión</span>
                        <span class="badge-soft">Citas en línea 24/7</span>
                    </div>
                    <h1 class="fw-bold mb-2">Bienvenido a {{ $clinica->nombre }}</h1>
                    <p class="mb-0 text-dark-emphasis">Un espacio diseñado para el bienestar de tu familia multiespecie. Reserva una cita al instante y accede a las mascotas registradas desde tu cuenta.</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show card-soft" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show card-soft" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @php
        $appointmentErrors = $errors->appointment ?? null;
        $defaultStylistId = old('entrenador_id');
        if (is_string($defaultStylistId) && str_contains($defaultStylistId, ':')) {
            $parts = array_values(array_filter(explode(':', $defaultStylistId), fn ($segment) => $segment !== ''));
            $defaultStylistId = end($parts) ?: reset($parts);
        }
        if (! $defaultStylistId && isset($estilistas) && $estilistas->count() === 1) {
            $defaultStylistId = optional($estilistas->first())->id;
        }
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-soft mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between flex-wrap gap-3 align-items-center mb-3">
                        <div>
                            <h2 class="section-title mb-1">Solicita tu cita</h2>
                            <p class="text-muted mb-0">Selecciona fecha, hora y el profesional que acompañará a tu mascota.</p>
                        </div>
                        <div class="badge-soft">Agenda rápida</div>
                    </div>
                    <form method="POST" action="{{ route('public.booking.appointment', $clinica) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-first-name">Nombre</label>
                                <input type="text" class="form-control @if($appointmentErrors?->has('nombres')) is-invalid @endif" id="appointment-first-name" name="nombres" value="{{ old('nombres', $ownerFirstName) }}" required>
                                @if($appointmentErrors?->has('nombres'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('nombres') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-last-name">Apellidos</label>
                                <input type="text" class="form-control @if($appointmentErrors?->has('apellidos')) is-invalid @endif" id="appointment-last-name" name="apellidos" value="{{ old('apellidos', $ownerLastName) }}">
                                @if($appointmentErrors?->has('apellidos'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('apellidos') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-whatsapp">WhatsApp</label>
                                <input type="text" class="form-control @if($appointmentErrors?->has('whatsapp')) is-invalid @endif" id="appointment-whatsapp" name="whatsapp" value="{{ old('whatsapp', $cliente->whatsapp ?? '') }}" required>
                                @if($appointmentErrors?->has('whatsapp'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('whatsapp') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-stylist">{{ $trainerLabelSingular }}</label>
                                <select class="form-select @if($appointmentErrors?->has('entrenador_id')) is-invalid @endif" id="appointment-stylist" name="entrenador_id" @if(($estilistas ?? collect())->isEmpty()) disabled @endif required>
                                    <option value="">Selecciona a tu {{ \Illuminate\Support\Str::lower($trainerLabelSingular) }}</option>
                                    @foreach($estilistas ?? [] as $estilista)
                                        @php
                                            $estilistaId = $estilista->id;
                                            if (is_string($estilistaId) && str_contains($estilistaId, ':')) {
                                                $idParts = array_values(array_filter(explode(':', $estilistaId), fn ($segment) => $segment !== ''));
                                                $estilistaId = end($idParts) ?: reset($idParts);
                                            }
                                        @endphp
                                        <option value="{{ $estilistaId }}" @selected((string) $defaultStylistId === (string) $estilistaId)>{{ trim($estilista->nombre . ' ' . ($estilista->apellidos ?? '')) }}</option>
                                    @endforeach
                                </select>
                                @if(($estilistas ?? collect())->isEmpty())
                                    <div class="form-text text-danger">No hay {{ \Illuminate\Support\Str::lower($trainerLabelPlural) }} disponibles por ahora.</div>
                                @endif
                                @if($appointmentErrors?->has('entrenador_id'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('entrenador_id') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-date">Fecha</label>
                                <input type="date" class="form-control @if($appointmentErrors?->has('fecha')) is-invalid @endif" id="appointment-date" name="fecha" value="{{ old('fecha') ?? now()->format('Y-m-d') }}" required>
                                @if($appointmentErrors?->has('fecha'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('fecha') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-time">Hora</label>
                                <select class="form-select @if($appointmentErrors?->has('hora')) is-invalid @endif" id="appointment-time" name="hora" required>
                                    <option value="">Selecciona un horario</option>
                                </select>
                                @if($appointmentErrors?->has('hora'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('hora') }}</div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Duración</label>
                                <p class="form-control-plaintext fw-semibold text-muted mb-0">{{ $defaultDuration }} minutos</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment-tipocita">Servicio</label>
                                <select class="form-select @if($appointmentErrors?->has('tipocita_id')) is-invalid @endif" id="appointment-tipocita" name="tipocita_id">
                                    <option value="">Selecciona una opción</option>
                                    @foreach($tipocitas as $tipo)
                                        <option value="{{ $tipo->id }}" @selected(old('tipocita_id') == $tipo->id)>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @if($appointmentErrors?->has('tipocita_id'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('tipocita_id') }}</div>
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="appointment-note">Notas adicionales</label>
                                <textarea class="form-control @if($appointmentErrors?->has('nota_cliente')) is-invalid @endif" id="appointment-note" name="nota_cliente" rows="3" placeholder="Cuéntanos detalles que debamos saber">{{ old('nota_cliente') }}</textarea>
                                @if($appointmentErrors?->has('nota_cliente'))
                                    <div class="invalid-feedback">{{ $appointmentErrors->first('nota_cliente') }}</div>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4 w-100">Solicitar cita</button>
                    </form>
                </div>
            </div>


        <div class="col-lg-4">
            @if(! $cliente)
                <div class="card card-soft mb-4">
                    <div class="card-body p-4">
                        <h3 class="section-title mb-2">Inicia sesión</h3>
                        <p class="text-muted mb-3">Usa el correo y la contraseña creada por la clínica para ver tus mascotas y documentos.</p>
                        <form method="POST" action="{{ route('public.booking.login', $clinica) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="login-email">Correo</label>
                                <input type="email" class="form-control @if(($errors->login ?? null)?->has('correo')) is-invalid @endif" id="login-email" name="correo" value="{{ old('correo') }}" required>
                                @if(($errors->login ?? null)?->has('correo'))
                                    <div class="invalid-feedback">{{ $errors->login->first('correo') }}</div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="login-password">Contraseña</label>
                                <input type="password" class="form-control @if(($errors->login ?? null)?->has('password')) is-invalid @endif" id="login-password" name="password" required>
                                @if(($errors->login ?? null)?->has('password'))
                                    <div class="invalid-feedback">{{ $errors->login->first('password') }}</div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>
                </div>
            @endif
            @if($cliente)
                <div class="card card-soft mb-4">
                    <div class="card-body p-4">
                        <h3 class="section-title mb-2">Tu cuenta</h3>
                        <p class="text-muted mb-3">Sesión activa para {{ $cliente->email ?? 'tu cuenta' }}.</p>
                        <form method="POST" action="{{ route('public.booking.logout', $clinica) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card card-soft mb-4">
                <div class="card-body p-4">
                    <h3 class="section-title mb-3">Información de contacto</h3>
                    <div class="d-grid gap-3">
                        @if($contactPhone)
                            <div class="contact-item">
                                <span class="contact-dot"></span>
                                <div>
                                    <div class="fw-semibold">Teléfono / WhatsApp</div>
                                    <div class="text-muted">{{ $contactPhone }}</div>
                                </div>
                            </div>
                        @endif
                        @if($contactEmail)
                            <div class="contact-item">
                                <span class="contact-dot"></span>
                                <div>
                                    <div class="fw-semibold">Correo</div>
                                    <div class="text-muted">{{ $contactEmail }}</div>
                                </div>
                            </div>
                        @endif
                        @if($contactAddress)
                            <div class="contact-item">
                                <span class="contact-dot"></span>
                                <div>
                                    <div class="fw-semibold">Dirección</div>
                                    <div class="text-muted">{{ $contactAddress }}</div>
                                </div>
                            </div>
                        @endif
                        @if(! $contactPhone && ! $contactEmail && ! $contactAddress)
                            <div class="text-muted">Agrega los datos de contacto de tu clínica para mostrarlos aquí.</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($cliente && $proximasReservas->isNotEmpty())
                <div class="card card-soft mb-4">
                    <div class="card-body p-4">
                        <h3 class="section-title mb-3">Tus próximas solicitudes</h3>
                        @foreach($proximasReservas as $reserva)
                            <div class="timeline-item">
                                <h4 class="h6 mb-1">{{ $reserva->tipo ?? 'Reserva' }} · {{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y H:i') }}</h4>
                                <p class="mb-1 text-muted">Estado: <strong>{{ $reserva->estado }}</strong> · Duración: {{ $reserva->duracion }} minutos</p>
                                @if($reserva->nota_cliente)
                                    <p class="mb-0 small">Nota: {{ $reserva->nota_cliente }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateInput = document.getElementById('appointment-date');
        const timeSelect = document.getElementById('appointment-time');
        const stylistSelect = document.getElementById('appointment-stylist');
        const storedTime = @json(old('hora'));
        const availabilityUrl = @json(route('public.booking.availability', $clinica));
        const stylistLabelSingular = @json($trainerLabelSingular);
        const stylistLabelPlural = @json($trainerLabelPlural);

        const setTimePlaceholder = (message) => {
            if (!timeSelect) {
                return;
            }
            timeSelect.innerHTML = `<option value="">${message}</option>`;
            timeSelect.disabled = true;
        };

        const fetchSlots = () => {
            if (!dateInput || !timeSelect) {
                return;
            }

            const dateValue = dateInput.value;
            const stylistValue = stylistSelect ? stylistSelect.value : '';

            if (!dateValue) {
                setTimePlaceholder('Selecciona una fecha');
                return;
            }

            if (stylistSelect && !stylistValue) {
                setTimePlaceholder(`Selecciona a tu ${stylistLabelSingular}`);
                return;
            }

            timeSelect.innerHTML = '<option value="">Cargando horarios...</option>';
            timeSelect.disabled = true;

            const params = new URLSearchParams({ date: dateValue });
            if (stylistValue) {
                const normalizedStylist = stylistValue.includes(':')
                    ? stylistValue.split(':').filter(Boolean).pop()
                    : stylistValue;
                if (normalizedStylist) {
                    params.append('entrenador_id', normalizedStylist);
                }
            }

            fetch(`${availabilityUrl}?${params.toString()}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('No se pudo obtener disponibilidad');
                    }
                    return response.json();
                })
                .then(data => {
                    timeSelect.innerHTML = '<option value="">Selecciona un horario</option>';
                    if (Array.isArray(data.slots) && data.slots.length) {
                        timeSelect.disabled = false;
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = slot;
                            if (storedTime && storedTime === slot) {
                                option.selected = true;
                            }
                            timeSelect.appendChild(option);
                        });
                    } else {
                        setTimePlaceholder('Sin horarios disponibles');
                    }
                })
                .catch(() => {
                    setTimePlaceholder('No se pudo cargar la disponibilidad');
                });
        };

        if (dateInput) {
            dateInput.addEventListener('change', fetchSlots);
        }
        if (stylistSelect) {
            stylistSelect.addEventListener('change', fetchSlots);
        }
        fetchSlots();
    });
</script>
</body>
</html>
