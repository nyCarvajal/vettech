<div class="row g-3">
    @php
        $featureDefaults = \App\Models\Clinica::featureDefaults();
        $featureOptions = [
            'agenda' => [
                'label' => 'Agenda',
                'description' => 'Citas, reservas y calendario.',
            ],
            'facturacion_pos' => [
                'label' => 'Facturación POS',
                'description' => 'Cobros rápidos y emisión de facturas.',
            ],
            'tutores' => [
                'label' => 'Tutores',
                'description' => 'Gestión de propietarios de mascotas.',
            ],
            'pacientes' => [
                'label' => 'Pacientes',
                'description' => 'Registro y seguimiento clínico.',
            ],
            'dispensacion' => [
                'label' => 'Dispensario',
                'description' => 'Entrega y control de medicamentos.',
            ],
            'hospitalizacion' => [
                'label' => 'Hospitalización 24/7',
                'description' => 'Manejo de estancias y monitoreo.',
            ],
            'cirugia' => [
                'label' => 'Cirugía y procedimientos',
                'description' => 'Registro y seguimiento de cirugías.',
            ],
            'belleza' => [
                'label' => 'Peluquería',
                'description' => 'Servicios de grooming y estética.',
            ],
            'consentimientos' => [
                'label' => 'Consentimientos',
                'description' => 'Documentos clínicos firmados.',
            ],
            'plantillas_consentimientos' => [
                'label' => 'Plantillas de consentimientos',
                'description' => 'Modelos reutilizables de formularios.',
            ],
            'arqueo_caja' => [
                'label' => 'Arqueo de caja',
                'description' => 'Cierres y consolidación de caja.',
            ],
            'reportes_basicos' => [
                'label' => 'Reportes básicos',
                'description' => 'Indicadores rápidos del negocio.',
            ],
            'reportes_avanzados' => [
                'label' => 'Reportes avanzados',
                'description' => 'Reportes detallados y analíticos.',
            ],
            'config_clinica' => [
                'label' => 'Configuración de clínica',
                'description' => 'Acceso al panel de ajustes.',
            ],
        ];
    @endphp
    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $clinica->nombre) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="{{ old('slug', $clinica->slug) }}" placeholder="identificador-unico">
    </div>
    <div class="col-md-6">
        <label class="form-label">Correo</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $clinica->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $clinica->telefono) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color primario</label>
        <input type="text" name="color" class="form-control" value="{{ old('color', $clinica->color) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color del menú</label>
        <input type="text" name="menu_color" class="form-control" value="{{ old('menu_color', $clinica->menu_color) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color de la topbar</label>
        <input type="text" name="topbar_color" class="form-control" value="{{ old('topbar_color', $clinica->topbar_color) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Etiqueta singular del profesional</label>
        <input type="text" name="trainer_label_singular" class="form-control" value="{{ old('trainer_label_singular', $clinica->trainer_label_singular) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Etiqueta plural del profesional</label>
        <input type="text" name="trainer_label_plural" class="form-control" value="{{ old('trainer_label_plural', $clinica->trainer_label_plural) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">NIT</label>
        <input type="text" name="nit" class="form-control" value="{{ old('nit', $clinica->nit) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $clinica->direccion) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Municipio</label>
        <input type="number" name="municipio" class="form-control" value="{{ old('municipio', $clinica->municipio) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Mensaje de bienvenida</label>
        <textarea name="msj_bienvenida" class="form-control" rows="2">{{ old('msj_bienvenida', $clinica->msj_bienvenida) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Mensaje de confirmación</label>
        <textarea name="msj_reserva_confirmada" class="form-control" rows="2">{{ old('msj_reserva_confirmada', $clinica->msj_reserva_confirmada) }}</textarea>
    </div>
    <div class="col-md-12">
        <label class="form-label">Mensaje de cierre</label>
        <textarea name="msj_finalizado" class="form-control" rows="2">{{ old('msj_finalizado', $clinica->msj_finalizado) }}</textarea>
    </div>
    <div class="col-12">
        <div class="border rounded-3 p-3 bg-light">
            <h5 class="mb-2">Módulos del plan</h5>
            <p class="text-muted mb-3">Activa los módulos disponibles para esta clínica.</p>
            <div class="row g-3">
                @foreach ($featureOptions as $key => $feature)
                    <div class="col-md-6">
                        <label class="border rounded-3 p-3 w-100 h-100 d-flex gap-3 align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $feature['label'] }}</div>
                                <div class="text-muted small">{{ $feature['description'] }}</div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="features[{{ $key }}]" value="0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="features[{{ $key }}]"
                                    value="1"
                                    @checked(old("features.{$key}", $clinica->featureEnabled($key, $featureDefaults[$key])))
                                >
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
