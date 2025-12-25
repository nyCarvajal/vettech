@php
    $paraclinicosIniciales = $historia->relationLoaded('paraclinicos') ? $historia->paraclinicos : $historia->paraclinicos()->get();
    $diagnosticosIniciales = $historia->relationLoaded('diagnosticos') ? $historia->diagnosticos : $historia->diagnosticos()->get();
    $selectedPaciente = $pacientes->firstWhere('id', old('paciente_id', $historia->paciente_id));
    $pacienteIniciales = $selectedPaciente
        ? collect([$selectedPaciente->nombres, $selectedPaciente->apellidos])
            ->filter()
            ->map(fn($parte) => mb_substr($parte, 0, 1))
            ->join('')
        : 'P';
@endphp

@push('styles')
    <style>
        .consultation-hero {
            background: linear-gradient(135deg, #4c6fff, #7d3cff);
            border: none;
            color: #fff;
        }

        .pill-badge {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 600;
        }

        .info-card {
            border: 1px solid #e9ecef;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.05);
        }

        .section-title {
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .tag-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .metric-chip {
            border: 1px dashed #d0d5dd;
            border-radius: 14px;
            padding: 0.85rem 1rem;
            background: #f8fafc;
        }

        .textarea-soft,
        .form-control.soft {
            background: #f8fafc;
            border-radius: 12px;
        }

        .divider-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #eef2ff;
            color: #4338ca;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .table-soft {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-soft th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.82rem;
        }

        .badge-soft {
            background: #eef2ff;
            color: #4338ca;
            font-weight: 700;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
        }

        .icon-circle {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #eef2ff;
            color: #4338ca;
            font-size: 1.2rem;
        }
    </style>
@endpush

<div class="card consultation-hero mb-4">
    <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center gap-3">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <div class="rounded-circle bg-white bg-opacity-15 text-white d-flex align-items-center justify-content-center"
                style="width: 72px; height: 72px; font-weight: 800; font-size: 1.5rem;">
                {{ $pacienteIniciales }}
            </div>
            <div>
                <p class="text-white-50 mb-1">Formato general de historia clínica</p>
                <h2 class="mb-1">Consulta veterinaria</h2>
                <div class="d-flex flex-wrap gap-2">
                    <span class="pill-badge"><i class="ri-hashtag"></i>HC {{ $historia->id ?? 'Borrador nuevo' }}</span>
                    <span class="pill-badge"><i class="ri-calendar-line"></i>{{ now()->format('d M Y, H:i') }}</span>
                    <span class="pill-badge"><i class="ri-stethoscope-line"></i>Estado: {{ ucfirst($historia->estado ?? 'borrador') }}</span>
                </div>
            </div>
        </div>
        <div class="text-lg-end">
            <div class="badge-soft d-inline-flex align-items-center gap-2 mb-2" id="autosave-status">
                <i class="ri-save-2-line"></i> Borrador no guardado aún.
            </div>
            @if (!$selectedPaciente)
                <div class="mt-2">
                    <label for="paciente_id" class="form-label text-white-75">Selecciona paciente para bloquear datos</label>
                    <select name="paciente_id" id="paciente_id" class="form-select">
                        <option value="">Buscar paciente</option>
                        @foreach ($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('paciente_id', $historia->paciente_id) == $paciente->id)>
                                {{ $paciente->nombres }} {{ $paciente->apellidos }} ({{ $paciente->numero_documento ?? 'Sin documento' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
</div>

@if ($selectedPaciente)
    <input type="hidden" name="paciente_id" value="{{ $selectedPaciente->id }}">
@endif
<input type="hidden" name="estado" id="estado" value="{{ old('estado', $historia->estado ?? 'borrador') }}">
<input type="hidden" name="historia_id" id="historia_id" value="{{ $historia->id }}">

<div class="info-card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <p class="tag-label mb-1">1. Identificación</p>
                <h5 class="section-title mb-0">Datos de paciente y cliente</h5>
                <small class="text-muted">Información bloqueada para mantener trazabilidad clínica.</small>
            </div>
            <span class="badge-soft d-inline-flex align-items-center gap-2"><i class="ri-shield-user-line"></i>Verificados</span>
        </div>
        <div class="row g-3">
            <div class="col-lg-4">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="icon-circle"><i class="ri-heart-2-line"></i></span>
                        <div>
                            <p class="mb-0 text-muted small">Paciente</p>
                            <h6 class="mb-0">{{ $selectedPaciente?->nombres }} {{ $selectedPaciente?->apellidos }}</h6>
                        </div>
                    </div>
                    <p class="mb-1 small"><strong>Documento:</strong> {{ $selectedPaciente?->tipo_documento }} {{ $selectedPaciente?->numero_documento }}</p>
                    <p class="mb-1 small"><strong>Sexo:</strong> {{ $selectedPaciente?->sexo ? ucfirst($selectedPaciente->sexo) : 'Sin dato' }}</p>
                    <p class="mb-0 small"><strong>Fecha nacimiento:</strong> {{ optional($selectedPaciente?->fecha_nacimiento)->format('d/m/Y') ?? 'Sin registrar' }}</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="icon-circle"><i class="ri-user-3-line"></i></span>
                        <div>
                            <p class="mb-0 text-muted small">Propietario / Responsable</p>
                            <h6 class="mb-0">{{ $selectedPaciente?->acompanante ?? 'No registrado' }}</h6>
                        </div>
                    </div>
                    <p class="mb-1 small"><strong>Contacto:</strong> {{ $selectedPaciente?->acompanante_contacto ?? 'Sin número' }}</p>
                    <p class="mb-1 small"><strong>WhatsApp:</strong> {{ $selectedPaciente?->whatsapp ?? 'No configurado' }}</p>
                    <p class="mb-0 small"><strong>Correo:</strong> {{ $selectedPaciente?->email ?? 'Sin correo' }}</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="icon-circle"><i class="ri-map-pin-line"></i></span>
                        <div>
                            <p class="mb-0 text-muted small">Ubicación</p>
                            <h6 class="mb-0">{{ $selectedPaciente?->ciudad ?? 'Ciudad sin registrar' }}</h6>
                        </div>
                    </div>
                    <p class="mb-1 small"><strong>Dirección:</strong> {{ $selectedPaciente?->direccion ?? 'No definida' }}</p>
                    <p class="mb-1 small"><strong>Color / Pelaje:</strong> {{ $selectedPaciente?->color ?? 'Sin dato' }}</p>
                    <p class="mb-0 small"><strong>Microchip:</strong> {{ $selectedPaciente?->microchip ?? 'No asignado' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="info-card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <p class="tag-label mb-1">2. Anamnesis</p>
                <h5 class="section-title mb-0">Motivo de consulta y enfermedad actual</h5>
            </div>
            <span class="divider-label"><i class="ri-customer-service-2-line"></i> Datos narrados</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="motivo_consulta">Motivo de consulta</label>
                <textarea name="motivo_consulta" id="motivo_consulta" class="form-control textarea-soft" rows="3"
                    placeholder="Describe la razón principal de la visita">{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="enfermedad_actual">Enfermedad actual</label>
                <textarea name="enfermedad_actual" id="enfermedad_actual" class="form-control textarea-soft" rows="3"
                    placeholder="Evolución y síntomas según el propietario">{{ old('enfermedad_actual', $historia->enfermedad_actual) }}</textarea>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label" for="revision_sistemas">Revisión por sistemas</label>
                <textarea name="revision_sistemas" id="revision_sistemas" class="form-control textarea-soft" rows="2"
                    placeholder="Registro breve de hallazgos sistémicos">{{ old('revision_sistemas', $historia->revision_sistemas) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_farmacologicos">Antecedentes farmacológicos</label>
                <textarea name="antecedentes_farmacologicos" id="antecedentes_farmacologicos" class="form-control textarea-soft" rows="2"
                    placeholder="Medicamentos en uso, alergias a fármacos">{{ old('antecedentes_farmacologicos', $historia->antecedentes_farmacologicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_patologicos">Antecedentes patológicos</label>
                <textarea name="antecedentes_patologicos" id="antecedentes_patologicos" class="form-control textarea-soft" rows="2"
                    placeholder="Enfermedades previas, hospitalizaciones">{{ old('antecedentes_patologicos', $historia->antecedentes_patologicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_toxicologicos">Antecedentes toxicológicos</label>
                <textarea name="antecedentes_toxicologicos" id="antecedentes_toxicologicos" class="form-control textarea-soft" rows="2"
                    placeholder="Exposición a tóxicos, plantas, productos">{{ old('antecedentes_toxicologicos', $historia->antecedentes_toxicologicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_alergicos">Antecedentes alérgicos</label>
                <textarea name="antecedentes_alergicos" id="antecedentes_alergicos" class="form-control textarea-soft" rows="2"
                    placeholder="Reacciones a alimentos o medicamentos">{{ old('antecedentes_alergicos', $historia->antecedentes_alergicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_inmunologicos">Antecedentes inmunológicos</label>
                <textarea name="antecedentes_inmunologicos" id="antecedentes_inmunologicos" class="form-control textarea-soft" rows="2"
                    placeholder="Vacunación, desparasitaciones, pruebas">{{ old('antecedentes_inmunologicos', $historia->antecedentes_inmunologicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_quirurgicos">Antecedentes quirúrgicos</label>
                <textarea name="antecedentes_quirurgicos" id="antecedentes_quirurgicos" class="form-control textarea-soft" rows="2"
                    placeholder="Cirugías previas y fechas">{{ old('antecedentes_quirurgicos', $historia->antecedentes_quirurgicos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="antecedentes_ginecologicos">Ginecológicos / reproductivos</label>
                <textarea name="antecedentes_ginecologicos" id="antecedentes_ginecologicos" class="form-control textarea-soft" rows="3"
                    placeholder="Celo, partos, esterilización">{{ old('antecedentes_ginecologicos', $historia->antecedentes_ginecologicos) }}</textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label" for="antecedentes_familiares">Antecedentes familiares</label>
                <textarea name="antecedentes_familiares" id="antecedentes_familiares" class="form-control textarea-soft" rows="3"
                    placeholder="Patologías hereditarias o frecuentes en la línea">{{ old('antecedentes_familiares', $historia->antecedentes_familiares) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="info-card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <p class="tag-label mb-1">3. Examen físico</p>
                <h5 class="section-title mb-0">Signos vitales y valoración general</h5>
            </div>
            <span class="divider-label"><i class="ri-heart-pulse-line"></i> Valores actuales</span>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="frecuencia_cardiaca">Frecuencia cardiaca</label>
                <div class="metric-chip">
                    <input type="number" class="form-control soft" name="frecuencia_cardiaca" id="frecuencia_cardiaca"
                        value="{{ old('frecuencia_cardiaca', $historia->frecuencia_cardiaca) }}" placeholder="Lpm">
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="tension_arterial">Tensión arterial</label>
                <div class="metric-chip">
                    <input type="text" class="form-control soft" name="tension_arterial" id="tension_arterial"
                        value="{{ old('tension_arterial', $historia->tension_arterial) }}" placeholder="mmHg">
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="saturacion_oxigeno">Saturación O₂</label>
                <div class="metric-chip">
                    <input type="number" step="0.1" class="form-control soft" name="saturacion_oxigeno" id="saturacion_oxigeno"
                        value="{{ old('saturacion_oxigeno', $historia->saturacion_oxigeno) }}" placeholder="%">
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="frecuencia_respiratoria">Frecuencia respiratoria</label>
                <div class="metric-chip">
                    <input type="number" class="form-control soft" name="frecuencia_respiratoria" id="frecuencia_respiratoria"
                        value="{{ old('frecuencia_respiratoria', $historia->frecuencia_respiratoria) }}" placeholder="Rpm">
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="examen_cabeza_cuello">Cabeza y cuello</label>
                <textarea class="form-control textarea-soft" name="examen_cabeza_cuello" id="examen_cabeza_cuello" rows="2"
                    placeholder="Mucosas, ganglios, cavidad oral">{{ old('examen_cabeza_cuello', $historia->examen_cabeza_cuello) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_torax">Tórax</label>
                <textarea class="form-control textarea-soft" name="examen_torax" id="examen_torax" rows="2"
                    placeholder="Auscultación pulmonar y cardiaca">{{ old('examen_torax', $historia->examen_torax) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_corazon">Corazón</label>
                <textarea class="form-control textarea-soft" name="examen_corazon" id="examen_corazon" rows="2"
                    placeholder="Ruidos, soplos, ritmo">{{ old('examen_corazon', $historia->examen_corazon) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_mama">Mamas</label>
                <textarea class="form-control textarea-soft" name="examen_mama" id="examen_mama" rows="2"
                    placeholder="Hallazgos mamarios">{{ old('examen_mama', $historia->examen_mama) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_abdomen">Abdomen</label>
                <textarea class="form-control textarea-soft" name="examen_abdomen" id="examen_abdomen" rows="2"
                    placeholder="Dolor, masas, motilidad">{{ old('examen_abdomen', $historia->examen_abdomen) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_genitales">Genitales</label>
                <textarea class="form-control textarea-soft" name="examen_genitales" id="examen_genitales" rows="2"
                    placeholder="Secreciones, integridad">{{ old('examen_genitales', $historia->examen_genitales) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_neurologico">Neurológico</label>
                <textarea class="form-control textarea-soft" name="examen_neurologico" id="examen_neurologico" rows="2"
                    placeholder="Reflejos, estado mental">{{ old('examen_neurologico', $historia->examen_neurologico) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="examen_extremidades">Extremidades / locomotor</label>
                <textarea class="form-control textarea-soft" name="examen_extremidades" id="examen_extremidades" rows="2"
                    placeholder="Claudicaciones, dolor, movilidad">{{ old('examen_extremidades', $historia->examen_extremidades) }}</textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label" for="examen_piel">Piel y faneras</label>
                <textarea class="form-control textarea-soft" name="examen_piel" id="examen_piel" rows="2"
                    placeholder="Lesiones, prurito, ectoparásitos">{{ old('examen_piel', $historia->examen_piel) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="info-card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <p class="tag-label mb-1">4. Paraclínicos</p>
                <h5 class="section-title mb-0">Órdenes y resultados</h5>
            </div>
            <span class="divider-label"><i class="ri-flask-line"></i> Laboratorios e imágenes</span>
        </div>
        <div class="row g-2 align-items-center mb-3">
            <div class="col-md-8">
                <input type="text" id="paraclinico-nombre" class="form-control soft" placeholder="Buscar o escribir examen">
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-outline-primary w-100" id="agregar-paraclinico"><i class="ri-add-line"></i> Agregar examen</button>
            </div>
        </div>
        <div id="paraclinicos-lista" class="row g-3"></div>
    </div>
</div>

<div class="info-card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <p class="tag-label mb-1">5. Diagnóstico</p>
                <h5 class="section-title mb-0">Hipótesis y confirmaciones</h5>
            </div>
            <span class="divider-label"><i class="ri-file-list-3-line"></i> Problemas activos</span>
        </div>
        <div class="row g-2 mb-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="diagnostico-codigo">Código</label>
                <input type="text" id="diagnostico-codigo" class="form-control soft" placeholder="Código CIE10">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="diagnostico-descripcion">Descripción</label>
                <input type="text" id="diagnostico-descripcion" class="form-control soft" placeholder="Escribe el diagnóstico">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="diagnostico-confirmado">Estado</label>
                <select id="diagnostico-confirmado" class="form-select soft">
                    <option value="1">Confirmado</option>
                    <option value="0">No confirmado</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary mb-3" id="agregar-diagnostico"><i class="ri-check-line"></i> Agregar diagnóstico</button>
        <div id="diagnosticos-lista" class="row g-3"></div>
    </div>
</div>

<div class="info-card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <p class="tag-label mb-1">6. Plan</p>
                <h5 class="section-title mb-0">Análisis clínico y acciones</h5>
            </div>
            <span class="divider-label"><i class="ri-mental-health-line"></i> Resumen profesional</span>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label" for="analisis">Análisis</label>
                <textarea name="analisis" id="analisis" class="form-control textarea-soft" rows="3"
                    placeholder="Conclusiones clínicas y correlación de hallazgos">{{ old('analisis', $historia->analisis) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="plan_procedimientos">Procedimientos</label>
                <textarea name="plan_procedimientos" id="plan_procedimientos" class="form-control textarea-soft" rows="3"
                    placeholder="Procedimientos recomendados">{{ old('plan_procedimientos', $historia->plan_procedimientos) }}</textarea>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="plan_medicamentos">Medicamentos</label>
                    <a href="https://mipres.sispro.gov.co/Autenticacion/Login.aspx" target="_blank" rel="noopener" class="small">Ir a MIPRES</a>
                </div>
                <textarea name="plan_medicamentos" id="plan_medicamentos" class="form-control textarea-soft" rows="3"
                    placeholder="Dosis, vía y duración">{{ old('plan_medicamentos', $historia->plan_medicamentos) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="mipres_url">URL o radicado MIPRES (opcional)</label>
                <input type="text" class="form-control soft" name="mipres_url" id="mipres_url"
                    value="{{ old('mipres_url', $historia->mipres_url) }}" placeholder="Pega aquí el enlace o número de solicitud">
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary" data-estado="definitiva"><i class="ri-save-line"></i> Guardar definitivo</button>
        <button type="button" class="btn btn-outline-secondary" id="guardar-borrador"><i class="ri-draft-line"></i> Guardar borrador</button>
    </div>
    <a href="{{ route('historias-clinicas.index') }}" class="btn btn-link">Volver</a>
</div>

<input type="hidden" name="paraclinicos_json" id="paraclinicos_json">
<input type="hidden" name="diagnosticos_json" id="diagnosticos_json">

@push('scripts')
<script>
    const paraclinicosLista = document.getElementById('paraclinicos-lista');
    const diagnosticosLista = document.getElementById('diagnosticos-lista');
    const paraclinicosJson = document.getElementById('paraclinicos_json');
    const diagnosticosJson = document.getElementById('diagnosticos_json');
    const historiaIdInput = document.getElementById('historia_id');
    const estadoInput = document.getElementById('estado');
    const autosaveStatus = document.getElementById('autosave-status');
    const form = document.getElementById('historia-clinica-form');
    const autosaveUrl = form.dataset.autosave;
    const updateUrlTemplate = form.dataset.updateUrl;

    let paraclinicos = @json($paraclinicosIniciales->map(fn($p) => ['nombre' => $p->nombre, 'resultado' => $p->resultado]));
    let diagnosticos = @json($diagnosticosIniciales->map(fn($d) => ['codigo' => $d->codigo, 'descripcion' => $d->descripcion, 'confirmado' => (bool) $d->confirmado]));
    let autosaveTimer = null;

    function renderParaclinicos() {
        paraclinicosLista.innerHTML = '';
        paraclinicos.forEach((item, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-12';
            wrapper.innerHTML = `
                <div class="border rounded p-3 bg-light">\
                    <div class="d-flex justify-content-between align-items-start mb-2">\
                        <div class="d-flex align-items-center gap-2">\
                            <span class="icon-circle"><i class="ri-flask-line"></i></span>\
                            <div>\
                                <strong>${item.nombre}</strong>\
                                <div class="text-muted small">Resultado</div>\
                            </div>\
                        </div>\
                        <button type="button" class="btn btn-sm btn-link text-danger" data-remove-paraclinico="${index}">Eliminar</button>\
                    </div>\
                    <textarea class="form-control" data-paraclinico-resultado="${index}" rows="2" placeholder="Anota resultados o estado">${item.resultado ?? ''}</textarea>\
                </div>`;
            paraclinicosLista.appendChild(wrapper);
        });
        paraclinicosJson.value = JSON.stringify(paraclinicos);
    }

    function renderDiagnosticos() {
        diagnosticosLista.innerHTML = '';
        diagnosticos.forEach((item, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-12';
            wrapper.innerHTML = `
                <div class="border rounded p-3 bg-light">\
                    <div class="d-flex justify-content-between align-items-center mb-2">\
                        <div>\
                            <div class="text-muted">${item.codigo || 'Sin código'}</div>\
                            <div class="fw-semibold">${item.descripcion}</div>\
                        </div>\
                        <div class="d-flex align-items-center gap-2">\
                            <span class="badge ${item.confirmado ? 'bg-success' : 'bg-warning text-dark'}">${item.confirmado ? 'Confirmado' : 'No confirmado'}</span>\
                            <button type="button" class="btn btn-sm btn-link text-danger" data-remove-diagnostico="${index}">Eliminar</button>\
                        </div>\
                    </div>\
                    <div class="row g-2">\
                        <div class="col-md-3">\
                            <label class="form-label">Código</label>\
                            <input class="form-control" data-diagnostico-codigo="${index}" value="${item.codigo ?? ''}">\
                        </div>\
                        <div class="col-md-6">\
                            <label class="form-label">Descripción</label>\
                            <input class="form-control" data-diagnostico-descripcion="${index}" value="${item.descripcion}">\
                        </div>\
                        <div class="col-md-3">\
                            <label class="form-label">Confirmado</label>\
                            <select class="form-select" data-diagnostico-confirmado="${index}">\
                                <option value="1" ${item.confirmado ? 'selected' : ''}>Confirmado</option>\
                                <option value="0" ${!item.confirmado ? 'selected' : ''}>No confirmado</option>\
                            </select>\
                        </div>\
                    </div>\
                </div>`;
            diagnosticosLista.appendChild(wrapper);
        });
        diagnosticosJson.value = JSON.stringify(diagnosticos);
    }

    function scheduleAutoSave() {
        if (autosaveTimer) {
            clearTimeout(autosaveTimer);
        }
        autosaveTimer = setTimeout(doAutoSave, 800);
    }

    async function doAutoSave() {
        paraclinicosJson.value = JSON.stringify(paraclinicos);
        diagnosticosJson.value = JSON.stringify(diagnosticos);
        autosaveStatus.textContent = 'Guardando...';

        const formData = new FormData(form);

        try {
            const response = await fetch(autosaveUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error('No se pudo guardar automáticamente');
            }

            const data = await response.json();
            if (!historiaIdInput.value && data.id) {
                historiaIdInput.value = data.id;
                if (!form.querySelector('input[name="_method"]')) {
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);
                }
                if (updateUrlTemplate) {
                    form.action = updateUrlTemplate.replace('__ID__', data.id);
                }
            }
            autosaveStatus.textContent = `Borrador guardado ${data.updated_at ?? ''}`;
        } catch (error) {
            autosaveStatus.textContent = 'Error guardando el borrador';
        }
    }

    const addParaclinico = document.getElementById('agregar-paraclinico');
    if (addParaclinico) {
        addParaclinico.addEventListener('click', () => {
            const input = document.getElementById('paraclinico-nombre');
            if (input.value.trim() === '') return;
            paraclinicos.push({ nombre: input.value.trim(), resultado: '' });
            input.value = '';
            renderParaclinicos();
            scheduleAutoSave();
        });
    }

    const addDiagnostico = document.getElementById('agregar-diagnostico');
    if (addDiagnostico) {
        addDiagnostico.addEventListener('click', () => {
            const codigo = document.getElementById('diagnostico-codigo').value.trim();
            const descripcion = document.getElementById('diagnostico-descripcion').value.trim();
            const confirmado = document.getElementById('diagnostico-confirmado').value === '1';
            if (descripcion === '') return;
            diagnosticos.push({ codigo, descripcion, confirmado });
            document.getElementById('diagnostico-codigo').value = '';
            document.getElementById('diagnostico-descripcion').value = '';
            renderDiagnosticos();
            scheduleAutoSave();
        });
    }

    paraclinicosLista.addEventListener('input', (event) => {
        const resultadoIndex = event.target.getAttribute('data-paraclinico-resultado');
        if (resultadoIndex !== null) {
            paraclinicos[resultadoIndex].resultado = event.target.value;
            scheduleAutoSave();
        }
    });

    diagnosticosLista.addEventListener('input', (event) => {
        const codigoIndex = event.target.getAttribute('data-diagnostico-codigo');
        const descripcionIndex = event.target.getAttribute('data-diagnostico-descripcion');
        const confirmadoIndex = event.target.getAttribute('data-diagnostico-confirmado');

        if (codigoIndex !== null) {
            diagnosticos[codigoIndex].codigo = event.target.value;
        }
        if (descripcionIndex !== null) {
            diagnosticos[descripcionIndex].descripcion = event.target.value;
        }
        if (confirmadoIndex !== null) {
            diagnosticos[confirmadoIndex].confirmado = event.target.value === '1';
        }
        scheduleAutoSave();
    });

    paraclinicosLista.addEventListener('click', (event) => {
        const index = event.target.getAttribute('data-remove-paraclinico');
        if (index !== null) {
            paraclinicos.splice(index, 1);
            renderParaclinicos();
            scheduleAutoSave();
        }
    });

    diagnosticosLista.addEventListener('click', (event) => {
        const index = event.target.getAttribute('data-remove-diagnostico');
        if (index !== null) {
            diagnosticos.splice(index, 1);
            renderDiagnosticos();
            scheduleAutoSave();
        }
    });

    form.querySelectorAll('input, textarea, select').forEach((input) => {
        if (input.id === 'paraclinico-nombre' || input.id === 'diagnostico-codigo' || input.id === 'diagnostico-descripcion' || input.id === 'diagnostico-confirmado') {
            return;
        }
        input.addEventListener('input', scheduleAutoSave);
        input.addEventListener('change', scheduleAutoSave);
    });

    const saveDraft = document.getElementById('guardar-borrador');
    if (saveDraft) {
        saveDraft.addEventListener('click', () => {
            estadoInput.value = 'borrador';
            scheduleAutoSave();
        });
    }

    form.addEventListener('submit', () => {
        paraclinicosJson.value = JSON.stringify(paraclinicos);
        diagnosticosJson.value = JSON.stringify(diagnosticos);
        const button = document.activeElement;
        if (button?.dataset?.estado) {
            estadoInput.value = button.dataset.estado;
        }
    });

    renderParaclinicos();
    renderDiagnosticos();
</script>
@endpush
