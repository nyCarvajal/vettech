@php
    $paraclinicosIniciales = $historia->relationLoaded('paraclinicos') ? $historia->paraclinicos : $historia->paraclinicos()->get();
    $diagnosticosIniciales = $historia->relationLoaded('diagnosticos') ? $historia->diagnosticos : $historia->diagnosticos()->get();
@endphp

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="paciente_id" class="form-label">Paciente</label>
                <select name="paciente_id" id="paciente_id" class="form-select" required>
                    <option value="">Selecciona un paciente</option>
                    @foreach ($pacientes as $paciente)
                        <option value="{{ $paciente->id }}"
                            @selected(old('paciente_id', $historia->paciente_id) == $paciente->id)>
                            {{ $paciente->nombres }} {{ $paciente->apellidos }} ({{ $paciente->numero_documento }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <input type="text" class="form-control" value="{{ $historia->estado ?? 'borrador' }}" readonly>
                <input type="hidden" name="estado" id="estado" value="{{ old('estado', $historia->estado ?? 'borrador') }}">
                <input type="hidden" name="historia_id" id="historia_id" value="{{ $historia->id }}">
            </div>
            <div class="col-md-3 text-md-end">
                <small class="text-muted d-block" id="autosave-status">Borrador no guardado aún.</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Motivo de consulta y enfermedad actual</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label" for="motivo_consulta">Motivo de consulta</label>
            <textarea name="motivo_consulta" id="motivo_consulta" class="form-control" rows="3">{{ old('motivo_consulta', $historia->motivo_consulta) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="enfermedad_actual">Enfermedad actual</label>
            <textarea name="enfermedad_actual" id="enfermedad_actual" class="form-control" rows="3">{{ old('enfermedad_actual', $historia->enfermedad_actual) }}</textarea>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Antecedentes</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_farmacologicos">Farmacológicos</label>
            <textarea name="antecedentes_farmacologicos" id="antecedentes_farmacologicos" class="form-control" rows="2">{{ old('antecedentes_farmacologicos', $historia->antecedentes_farmacologicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_patologicos">Patológicos</label>
            <textarea name="antecedentes_patologicos" id="antecedentes_patologicos" class="form-control" rows="2">{{ old('antecedentes_patologicos', $historia->antecedentes_patologicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_toxicologicos">Toxicológicos</label>
            <textarea name="antecedentes_toxicologicos" id="antecedentes_toxicologicos" class="form-control" rows="2">{{ old('antecedentes_toxicologicos', $historia->antecedentes_toxicologicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_alergicos">Alérgicos</label>
            <textarea name="antecedentes_alergicos" id="antecedentes_alergicos" class="form-control" rows="2">{{ old('antecedentes_alergicos', $historia->antecedentes_alergicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_inmunologicos">Inmunológicos</label>
            <textarea name="antecedentes_inmunologicos" id="antecedentes_inmunologicos" class="form-control" rows="2">{{ old('antecedentes_inmunologicos', $historia->antecedentes_inmunologicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_quirurgicos">Quirúrgicos</label>
            <textarea name="antecedentes_quirurgicos" id="antecedentes_quirurgicos" class="form-control" rows="2">{{ old('antecedentes_quirurgicos', $historia->antecedentes_quirurgicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_ginecologicos">Ginecológicos</label>
            <textarea name="antecedentes_ginecologicos" id="antecedentes_ginecologicos" class="form-control" rows="3" placeholder="Menarquía, FUM, fertilidad, ETS, anticonceptivos, ciclos, partos/abortos">{{ old('antecedentes_ginecologicos', $historia->antecedentes_ginecologicos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="antecedentes_familiares">Familiares</label>
            <textarea name="antecedentes_familiares" id="antecedentes_familiares" class="form-control" rows="3">{{ old('antecedentes_familiares', $historia->antecedentes_familiares) }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label" for="revision_sistemas">Revisión por sistemas (opcional)</label>
            <textarea name="revision_sistemas" id="revision_sistemas" class="form-control" rows="2">{{ old('revision_sistemas', $historia->revision_sistemas) }}</textarea>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Paraclínicos</div>
    <div class="card-body">
        <div class="row g-2 mb-3 align-items-center">
            <div class="col-md-8">
                <input type="text" id="paraclinico-nombre" class="form-control" placeholder="Buscar o escribir examen">
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-outline-primary w-100" id="agregar-paraclinico">Agregar examen</button>
            </div>
        </div>
        <div id="paraclinicos-lista" class="row g-3"></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Signos vitales y examen físico</div>
    <div class="card-body row g-3">
        <div class="col-md-3">
            <label class="form-label" for="frecuencia_cardiaca">Frecuencia cardiaca</label>
            <input type="number" class="form-control" name="frecuencia_cardiaca" id="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca', $historia->frecuencia_cardiaca) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="tension_arterial">Tensión arterial</label>
            <input type="text" class="form-control" name="tension_arterial" id="tension_arterial" value="{{ old('tension_arterial', $historia->tension_arterial) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="saturacion_oxigeno">Saturación de oxígeno (%)</label>
            <input type="number" step="0.1" class="form-control" name="saturacion_oxigeno" id="saturacion_oxigeno" value="{{ old('saturacion_oxigeno', $historia->saturacion_oxigeno) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="frecuencia_respiratoria">Frecuencia respiratoria</label>
            <input type="number" class="form-control" name="frecuencia_respiratoria" id="frecuencia_respiratoria" value="{{ old('frecuencia_respiratoria', $historia->frecuencia_respiratoria) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label" for="examen_cabeza_cuello">Cabeza y cuello</label>
            <textarea class="form-control" name="examen_cabeza_cuello" id="examen_cabeza_cuello" rows="2">{{ old('examen_cabeza_cuello', $historia->examen_cabeza_cuello) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_torax">Tórax</label>
            <textarea class="form-control" name="examen_torax" id="examen_torax" rows="2">{{ old('examen_torax', $historia->examen_torax) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_corazon">Corazón</label>
            <textarea class="form-control" name="examen_corazon" id="examen_corazon" rows="2">{{ old('examen_corazon', $historia->examen_corazon) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_mama">Mama</label>
            <textarea class="form-control" name="examen_mama" id="examen_mama" rows="2">{{ old('examen_mama', $historia->examen_mama) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_abdomen">Abdomen</label>
            <textarea class="form-control" name="examen_abdomen" id="examen_abdomen" rows="2">{{ old('examen_abdomen', $historia->examen_abdomen) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_genitales">Genitales</label>
            <textarea class="form-control" name="examen_genitales" id="examen_genitales" rows="2">{{ old('examen_genitales', $historia->examen_genitales) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_neurologico">Neurológico</label>
            <textarea class="form-control" name="examen_neurologico" id="examen_neurologico" rows="2">{{ old('examen_neurologico', $historia->examen_neurologico) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_extremidades">Extremidades</label>
            <textarea class="form-control" name="examen_extremidades" id="examen_extremidades" rows="2">{{ old('examen_extremidades', $historia->examen_extremidades) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="examen_piel">Piel y faneras</label>
            <textarea class="form-control" name="examen_piel" id="examen_piel" rows="2">{{ old('examen_piel', $historia->examen_piel) }}</textarea>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Diagnóstico</div>
    <div class="card-body">
        <div class="row g-2 mb-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="diagnostico-codigo">Código</label>
                <input type="text" id="diagnostico-codigo" class="form-control" placeholder="Código CIE10">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="diagnostico-descripcion">Descripción</label>
                <input type="text" id="diagnostico-descripcion" class="form-control" placeholder="Escribe el diagnóstico">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select id="diagnostico-confirmado" class="form-select">
                    <option value="1">Confirmado</option>
                    <option value="0">No confirmado</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary mb-3" id="agregar-diagnostico">Agregar diagnóstico</button>
        <div id="diagnosticos-lista" class="row g-3"></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Análisis y plan</div>
    <div class="card-body row g-3">
        <div class="col-md-12">
            <label class="form-label" for="analisis">Análisis</label>
            <textarea name="analisis" id="analisis" class="form-control" rows="3">{{ old('analisis', $historia->analisis) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="plan_procedimientos">Procedimientos</label>
            <textarea name="plan_procedimientos" id="plan_procedimientos" class="form-control" rows="3">{{ old('plan_procedimientos', $historia->plan_procedimientos) }}</textarea>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label mb-0" for="plan_medicamentos">Medicamentos</label>
                <a href="https://mipres.sispro.gov.co/Autenticacion/Login.aspx" target="_blank" rel="noopener" class="small">Ir a MIPRES</a>
            </div>
            <textarea name="plan_medicamentos" id="plan_medicamentos" class="form-control" rows="3">{{ old('plan_medicamentos', $historia->plan_medicamentos) }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="mipres_url">URL o radicado MIPRES (opcional)</label>
            <input type="text" class="form-control" name="mipres_url" id="mipres_url" value="{{ old('mipres_url', $historia->mipres_url) }}" placeholder="Pega aquí el enlace o número de solicitud">
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center">
    <div>
        <button type="submit" class="btn btn-primary" data-estado="definitiva">Guardar definitivo</button>
        <button type="button" class="btn btn-outline-secondary" id="guardar-borrador">Guardar como borrador</button>
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
                <div class="border rounded p-3">\
                    <div class="d-flex justify-content-between align-items-start mb-2">\
                        <strong>${item.nombre}</strong>\
                        <button type="button" class="btn btn-sm btn-link text-danger" data-remove-paraclinico="${index}">Eliminar</button>\
                    </div>\
                    <label class="form-label">Resultado</label>\
                    <textarea class="form-control" data-paraclinico-resultado="${index}" rows="2">${item.resultado ?? ''}</textarea>\
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
                <div class="border rounded p-3">\
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

    document.getElementById('agregar-paraclinico').addEventListener('click', () => {
        const input = document.getElementById('paraclinico-nombre');
        if (input.value.trim() === '') return;
        paraclinicos.push({ nombre: input.value.trim(), resultado: '' });
        input.value = '';
        renderParaclinicos();
        scheduleAutoSave();
    });

    document.getElementById('agregar-diagnostico').addEventListener('click', () => {
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

    document.getElementById('guardar-borrador').addEventListener('click', () => {
        estadoInput.value = 'borrador';
        scheduleAutoSave();
    });

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
