@extends('layouts.app')

@section('content')
<div class="container patient-form">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-uppercase text-mint fw-semibold mb-1 small">Mascota de compañía</p>
            <h1 class="h4 mb-1 text-purple">{{ $patient->exists ? 'Editar paciente' : 'Nuevo paciente' }}</h1>
            <p class="text-muted mb-0">Registra al animal y vincúlalo con su tutor para mantener su historial clínico ordenado.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-purple">Volver</a>
    </div>

    @php
        $selectedWeightUnit = old('weight_unit', $patient->weight_unit ?? 'kg');
        $displayWeight = old('peso_actual');

        if ($displayWeight === null && $patient->peso_actual !== null) {
            $displayWeight = $selectedWeightUnit === 'g'
                ? $patient->peso_actual * 1000
                : $patient->peso_actual;
        }
    @endphp

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-4">
            <form method="post" action="{{ $patient->exists ? route('patients.update', $patient) : route('patients.store') }}" enctype="multipart/form-data" id="visible-patient-form">
                @csrf
                @if($patient->exists)
                    @method('PUT')
                @endif

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="glass-card h-100 p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-mint-soft text-mint"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 2a4 4 0 0 0-4 4c0 1.657 1 3 2 4.5S7 13 8 13s2-.843 2-2.5 2-2.843 2-4.5a4 4 0 0 0-4-4Z"/><path d="M3.8 13a5.7 5.7 0 0 1 8.4 0A7 7 0 0 1 8 15a7 7 0 0 1-4.2-2Z"/></svg></span>
                                <div>
                                    <p class="mb-0 text-muted small">Paso 1</p>
                                    <h6 class="mb-0 fw-semibold text-purple">Tutor(es) del paciente</h6>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Buscar tutor existente (nombre, teléfono, email o documento)</label>
                                <div class="input-group">
                                    <span class="input-group-text input-icon-mint">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z"/><path d="M15.854 15.146 11.5 10.793" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </span>
                                    <input type="search" id="tutorSearch" class="form-control" placeholder="Ej: Laura Gómez · 3115559999 · 123456" aria-label="Buscar tutor">
                                </div>
                                <div id="tutorSearchResults" class="list-group mt-2"></div>
                            </div>
                            <button type="button" class="btn btn-outline-purple w-100 mb-3" id="openTutorModal">Agregar tutor</button>

                            <label class="form-label text-muted mb-2">Tutores asociados</label>
                            <div class="table-responsive tutor-table">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Tutor</th>
                                            <th>Parentesco</th>
                                            <th class="text-center">Principal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tutoresBody"></tbody>
                                </table>
                            </div>
                            <input type="hidden" name="tutores_json" id="tutoresJson" value="{{ old('tutores_json') }}">
                            @error('tutores_json')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="glass-card h-100 p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-purple-soft text-purple"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1 3 3v5c0 3 2 5.5 5 7 3-1.5 5-4 5-7V3L8 1Z"/></svg></span>
                                <div>
                                    <p class="mb-0 text-muted small">Paso 2</p>
                                    <h6 class="mb-0 fw-semibold text-purple">Datos del animal de compañía</h6>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Foto del paciente</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $patient->photo_url }}" alt="Foto actual" class="rounded" style="width:72px;height:72px;object-fit:cover;">
                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M2 14s1.5-4 6-4 6 4 6 4H2Z"/></svg>
                                        </span>
                                        <input type="text" name="nombres" value="{{ old('nombres', $patient->nombres) }}" class="form-control" placeholder="Nombre de la mascota" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Especie</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-mint">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 2c1.5 0 2 .5 2 1s-.5 1-2 1-2-.5-2-1 .5-1 2-1Z"/><path d="M3 5c0 3 2 6 5 6s5-3 5-6"/></svg>
                                        </span>
                                        <select name="species_id" class="form-select fancy-select" required>
                                            <option value="">Selecciona especie</option>
                                            @foreach($species as $specie)
                                                <option value="{{ $specie->id }}" @selected(old('species_id', $patient->species_id) == $specie->id)>{{ $specie->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Raza</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4 4a4 4 0 0 1 8 0c0 2-2 5-4 5S4 6 4 4Z"/><path d="M6 11c0 1 1 3 2 3s2-2 2-3"/></svg>
                                        </span>
                                        <input type="text" name="breed_name" id="breedInput" class="form-control" list="breedOptions"
                                            value="{{ old('breed_name', $patient->breed?->name) }}" placeholder="Busca o escribe una raza">
                                        <input type="hidden" name="breed_id" id="breedId" value="{{ old('breed_id', $patient->breed_id) }}">
                                        <datalist id="breedOptions"></datalist>
                                    </div>
                                    <div class="form-text text-muted" id="breedHelp">Selecciona del catálogo o agrega una nueva raza.</div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2 d-none" id="breedCreateBtn">
                                        Agregar raza: <span id="breedCreateLabel"></span>
                                    </button>
                                    @error('breed_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Edad</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-mint">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1Z"/><path d="M8 4.5v3l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </span>
                                        <input type="number" name="age_value" min="0" value="{{ old('age_value', $patient->age_value) }}" class="form-control" placeholder="Edad">
                                        <select name="age_unit" class="form-select fancy-select">
                                            <option value="">Unidad</option>
                                            <option value="years" @selected(old('age_unit', $patient->age_unit) === 'years')>Años</option>
                                            <option value="months" @selected(old('age_unit', $patient->age_unit) === 'months')>Meses</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Peso</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3 4a5 5 0 0 1 10 0v7H3V4Z"/><path d="M5 11h6v2H5z"/></svg>
                                        </span>
                                        <input type="number" name="peso_actual" min="0" step="0.01" value="{{ $displayWeight }}" class="form-control" placeholder="Peso">
                                        <select name="weight_unit" class="form-select fancy-select">
                                            <option value="kg" @selected($selectedWeightUnit === 'kg')>Kilogramos</option>
                                            <option value="g" @selected($selectedWeightUnit === 'g')>Gramos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Microchip</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-mint">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 3h12v10H2z"/><path d="M5 1v2M11 1v2M5 13v2M11 13v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </span>
                                        <input type="text" name="microchip" value="{{ old('microchip', $patient->microchip) }}" class="form-control" placeholder="Código de microchip">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 2a6 6 0 1 0 6 6" /><path d="M8 4v4l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </span>
                                        <input type="text" name="estado" value="{{ old('estado', $patient->estado) }}" class="form-control" placeholder="Ej: estable, en tratamiento">
                                    </div>
                                    <div class="form-text text-muted">Indica el estado clínico general del paciente.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Carácter</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-mint">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5 3.5a3 3 0 1 1 6 0A3 3 0 0 1 5 3.5Z"/><path d="M2.5 14c1-2 3-3 5.5-3s4.5 1 5.5 3"/></svg>
                                        </span>
                                        <select name="temperamento" class="form-select fancy-select">
                                            <option value="">Selecciona carácter</option>
                                            @foreach(['tranquilo' => 'Tranquilo', 'nervioso' => 'Nervioso', 'agresivo' => 'Agresivo', 'miedoso' => 'Miedoso', 'otro' => 'Otro'] as $temp => $label)
                                                <option value="{{ $temp }}" @selected(old('temperamento', $patient->temperamento) === $temp)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alergias</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1 1 6l7 9 7-9-7-5Z"/></svg>
                                        </span>
                                        <textarea name="alergias" class="form-control" rows="2" placeholder="Describe alergias conocidas">{{ old('alergias', $patient->alergias) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sexo</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-mint">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M6 3a3 3 0 1 1-2 5.236V12H2V9.5H.5V8H2V6.236A3 3 0 0 1 6 3Z"/><path d="M10 5a3 3 0 1 1 2.5 4.764V16H11v-6.236A3 3 0 0 1 10 5Z"/></svg>
                                        </span>
                                        <select name="sexo" class="form-select fancy-select">
                                            <option value="">Prefiero no decir</option>
                                            <option value="M" @selected(old('sexo', $patient->sexo) === 'M')>Macho</option>
                                            <option value="F" @selected(old('sexo', $patient->sexo) === 'F')>Hembra</option>
                                            <option value="NA" @selected(old('sexo', $patient->sexo) === 'NA')>No aplica</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <div class="input-group">
                                        <span class="input-group-text input-icon-purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2h12v10H5l-3 3V2Z"/></svg>
                                        </span>
                                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Notas clínicas, comportamiento, cuidados especiales">{{ old('observaciones', $patient->observaciones) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="d-flex gap-2 align-items-center text-muted small">
                        <span class="dot dot-mint"></span> Campos clave para el tutor
                        <span class="dot dot-purple ms-3"></span> Datos esenciales del animal
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-purple">Cancelar</a>
                        <button class="btn btn-gradient" type="submit">{{ $patient->exists ? 'Actualizar' : 'Guardar' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="tutor-modal" id="tutorModal" aria-hidden="true">
    <div class="tutor-modal__backdrop" data-close-modal></div>
    <div class="tutor-modal__content">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h5 class="mb-1">Nuevo tutor</h5>
                <p class="text-muted small mb-0">Registra los datos básicos para asociarlos al paciente.</p>
            </div>
            <button type="button" class="btn-close" aria-label="Cerrar" data-close-modal></button>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombres</label>
                <input type="text" class="form-control" id="tutorNombres" placeholder="Nombres">
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="tutorApellidos" placeholder="Apellidos">
            </div>
            <div class="col-md-6">
                <label class="form-label">Documento</label>
                <input type="text" class="form-control" id="tutorDocumento" placeholder="Cédula o NIT">
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="tutorTelefono" placeholder="Número de contacto">
            </div>
            <div class="col-md-6">
                <label class="form-label">WhatsApp</label>
                <input type="text" class="form-control" id="tutorWhatsapp" placeholder="Número WhatsApp">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="tutorEmail" placeholder="Correo electrónico">
            </div>
            <div class="col-md-6">
                <label class="form-label">Dirección</label>
                <input type="text" class="form-control" id="tutorDireccion" placeholder="Dirección principal">
            </div>
            <div class="col-md-6">
                <label class="form-label">Ciudad</label>
                <input type="text" class="form-control" id="tutorCiudad" placeholder="Ciudad">
            </div>
            <div class="col-md-6">
                <label class="form-label">Parentesco</label>
                <input type="text" class="form-control" id="tutorParentesco" placeholder="Ej: propietario, cuidador">
            </div>
            <div class="col-md-6">
                <label class="form-label">Principal</label>
                <select class="form-select" id="tutorPrincipal">
                    <option value="1">Sí, es el principal</option>
                    <option value="0">No, es secundario</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" id="tutorObservaciones" rows="2" placeholder="Notas internas"></textarea>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-close-modal>Cancelar</button>
            <button type="button" class="btn btn-gradient" id="guardarTutor">Agregar tutor</button>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .patient-form .text-purple { color: #6f42c1; }
        .patient-form .text-mint { color: #2ac9a5; }
        .patient-form .bg-purple-soft { background: #f3e8ff; }
        .patient-form .bg-mint-soft { background: #e0fbf4; }
        .patient-form .btn-outline-purple { color: #6f42c1; border-color: #c9b1f5; }
        .patient-form .btn-outline-purple:hover { background: #6f42c1; color: #fff; }
        .patient-form .btn-gradient { background: linear-gradient(135deg, #6f42c1, #2ac9a5); border: none; color: #fff; }
        .patient-form .glass-card { background: linear-gradient(145deg, #faf7ff 0%, #f1fffa 100%); border-radius: 14px; border: 1px solid #ede7ff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .patient-form .input-group-text { border-width: 1.5px; }
        .patient-form .input-icon-purple { color: #6f42c1; background: linear-gradient(135deg, #f4e9ff, #ece0ff); border-color: #d8c4f7; }
        .patient-form .input-icon-mint { color: #1ba885; background: linear-gradient(135deg, #e6fbf5, #d5f6ed); border-color: #b9eddf; }
        .patient-form .fancy-select { border-color: #ede7ff; }
        .patient-form .fancy-select:focus, .patient-form .form-control:focus { border-color: #6f42c1; box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.15); }
        .patient-form .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .patient-form .dot-mint { background: #2ac9a5; }
        .patient-form .dot-purple { background: #6f42c1; }
        .patient-form .tutor-table { max-height: 260px; overflow-y: auto; border-radius: 12px; border: 1px solid #ede7ff; background: #fff; }
        .tutor-modal { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; z-index: 1050; }
        .tutor-modal.is-open { display: flex; }
        .tutor-modal__backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.5); }
        .tutor-modal__content { position: relative; background: #fff; border-radius: 16px; padding: 24px; width: min(720px, 92vw); max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18); }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ownersData = @json($owners->map(fn ($owner) => [
                'id' => $owner->id,
                'name' => $owner->name,
                'document' => $owner->document,
                'phone' => $owner->phone,
                'whatsapp' => $owner->whatsapp,
                'email' => $owner->email,
            ]));
            const tutorSearch = document.getElementById('tutorSearch');
            const tutorSearchResults = document.getElementById('tutorSearchResults');
            const tutorsBody = document.getElementById('tutoresBody');
            const tutorsJsonInput = document.getElementById('tutoresJson');
            const modal = document.getElementById('tutorModal');
            const openTutorModal = document.getElementById('openTutorModal');
            const guardarTutor = document.getElementById('guardarTutor');
            const breedInput = document.getElementById('breedInput');
            const breedIdInput = document.getElementById('breedId');
            const breedOptions = document.getElementById('breedOptions');
            const breedCreateBtn = document.getElementById('breedCreateBtn');
            const breedCreateLabel = document.getElementById('breedCreateLabel');
            const speciesSelect = document.querySelector('select[name="species_id"]');
            const breedsData = @json($breeds->map(fn ($breed) => [
                'id' => $breed->id,
                'name' => $breed->name,
                'species_id' => $breed->species_id,
            ]));

            let tutors = [];

            const normalize = (value) => (value || '').trim().toLowerCase().replace(/\s+/g, ' ');

            const updateTutorsJson = () => {
                tutorsJsonInput.value = JSON.stringify(tutors);
            };

            const renderTutors = () => {
                tutorsBody.innerHTML = '';
                tutors.forEach((tutor, index) => {
                    const row = document.createElement('tr');
                    const principalChecked = tutor.es_principal ? 'checked' : '';
                    row.innerHTML = `
                        <td>
                            <div class="fw-semibold">${tutor.nombre ?? ''}</div>
                            <div class="text-muted small">${tutor.documento ?? 'Sin documento'} · ${tutor.telefono ?? 'Sin teléfono'}</div>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" value="${tutor.parentesco ?? ''}" data-parentesco="${index}" placeholder="Ej: propietario">
                        </td>
                        <td class="text-center">
                            <input type="radio" name="tutor_principal" value="${index}" ${principalChecked} data-principal="${index}">
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-link text-danger" data-remove="${index}">Quitar</button>
                        </td>
                    `;
                    tutorsBody.appendChild(row);
                });
                updateTutorsJson();
            };

            const ensurePrincipal = () => {
                if (!tutors.some((tutor) => tutor.es_principal) && tutors.length > 0) {
                    tutors[0].es_principal = true;
                }
            };

            const openModal = () => {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            };

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            };

            const fillModalDefaults = () => {
                document.getElementById('tutorNombres').value = '';
                document.getElementById('tutorApellidos').value = '';
                document.getElementById('tutorDocumento').value = '';
                document.getElementById('tutorTelefono').value = '';
                document.getElementById('tutorWhatsapp').value = '';
                document.getElementById('tutorEmail').value = '';
                document.getElementById('tutorDireccion').value = '';
                document.getElementById('tutorCiudad').value = '';
                document.getElementById('tutorParentesco').value = '';
                document.getElementById('tutorObservaciones').value = '';
                document.getElementById('tutorPrincipal').value = tutors.length === 0 ? '1' : '0';
            };

            openTutorModal?.addEventListener('click', () => {
                fillModalDefaults();
                openModal();
            });

            modal?.addEventListener('click', (event) => {
                if (event.target.hasAttribute('data-close-modal')) {
                    closeModal();
                }
            });

            guardarTutor?.addEventListener('click', () => {
                const nombres = document.getElementById('tutorNombres').value.trim();
                const apellidos = document.getElementById('tutorApellidos').value.trim();
                const nombreCompleto = [nombres, apellidos].filter(Boolean).join(' ');
                if (!nombreCompleto) {
                    return;
                }

                tutors.push({
                    key: `nuevo-${Date.now()}`,
                    id: null,
                    nombre: nombreCompleto,
                    nombres,
                    apellidos,
                    documento: document.getElementById('tutorDocumento').value.trim() || null,
                    telefono: document.getElementById('tutorTelefono').value.trim() || null,
                    whatsapp: document.getElementById('tutorWhatsapp').value.trim() || null,
                    email: document.getElementById('tutorEmail').value.trim() || null,
                    direccion: document.getElementById('tutorDireccion').value.trim() || null,
                    ciudad: document.getElementById('tutorCiudad').value.trim() || null,
                    observaciones: document.getElementById('tutorObservaciones').value.trim() || null,
                    parentesco: document.getElementById('tutorParentesco').value.trim() || null,
                    es_principal: document.getElementById('tutorPrincipal').value === '1',
                    is_new: true,
                });
                ensurePrincipal();
                renderTutors();
                closeModal();
            });

            tutorSearch?.addEventListener('input', (event) => {
                const term = normalize(event.target.value);
                tutorSearchResults.innerHTML = '';
                if (!term) {
                    return;
                }
                const matches = ownersData.filter((owner) => {
                    const haystack = normalize([owner.name, owner.document, owner.phone, owner.email].join(' '));
                    return haystack.includes(term);
                }).slice(0, 5);
                matches.forEach((owner) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${owner.name} · ${owner.document ?? 'Sin documento'} · ${owner.phone ?? 'Sin teléfono'}`;
                    item.addEventListener('click', () => {
                        if (tutors.some((tutor) => tutor.id === owner.id)) {
                            tutorSearch.value = '';
                            tutorSearchResults.innerHTML = '';
                            return;
                        }
                        tutors.push({
                            key: `owner-${owner.id}`,
                            id: owner.id,
                            nombre: owner.name,
                            documento: owner.document,
                            telefono: owner.phone,
                            whatsapp: owner.whatsapp,
                            email: owner.email,
                            parentesco: null,
                            es_principal: tutors.length === 0,
                            is_new: false,
                        });
                        ensurePrincipal();
                        renderTutors();
                        tutorSearch.value = '';
                        tutorSearchResults.innerHTML = '';
                    });
                    tutorSearchResults.appendChild(item);
                });
            });

            tutorsBody?.addEventListener('input', (event) => {
                const index = event.target.getAttribute('data-parentesco');
                if (index !== null) {
                    tutors[index].parentesco = event.target.value;
                    updateTutorsJson();
                }
            });

            tutorsBody?.addEventListener('change', (event) => {
                const index = event.target.getAttribute('data-principal');
                if (index !== null) {
                    tutors = tutors.map((tutor, idx) => ({
                        ...tutor,
                        es_principal: idx === Number(index),
                    }));
                    renderTutors();
                }
            });

            tutorsBody?.addEventListener('click', (event) => {
                const index = event.target.getAttribute('data-remove');
                if (index !== null) {
                    tutors.splice(index, 1);
                    ensurePrincipal();
                    renderTutors();
                }
            });

            const fillBreeds = () => {
                const currentSpecies = speciesSelect?.value ? Number(speciesSelect.value) : null;
                breedOptions.innerHTML = '';
                breedsData.filter((breed) => !currentSpecies || breed.species_id === currentSpecies)
                    .forEach((breed) => {
                        const option = document.createElement('option');
                        option.value = breed.name;
                        breedOptions.appendChild(option);
                    });
            };

            const syncBreedSelection = () => {
                const name = normalize(breedInput.value);
                const currentSpecies = speciesSelect?.value ? Number(speciesSelect.value) : null;
                const match = breedsData.find((breed) => normalize(breed.name) === name && (!currentSpecies || breed.species_id === currentSpecies));
                if (match) {
                    breedIdInput.value = match.id;
                    breedCreateBtn.classList.add('d-none');
                } else {
                    breedIdInput.value = '';
                    if (name) {
                        breedCreateLabel.textContent = breedInput.value.trim();
                        breedCreateBtn.classList.remove('d-none');
                    } else {
                        breedCreateBtn.classList.add('d-none');
                    }
                }
            };

            breedInput?.addEventListener('input', syncBreedSelection);
            speciesSelect?.addEventListener('change', () => {
                fillBreeds();
                syncBreedSelection();
            });

            breedCreateBtn?.addEventListener('click', () => {
                breedCreateBtn.classList.add('btn-outline-success');
                breedCreateBtn.classList.remove('btn-outline-secondary');
            });

            const oldTutors = tutorsJsonInput.value;
            if (oldTutors) {
                try {
                    const parsed = JSON.parse(oldTutors);
                    if (Array.isArray(parsed)) {
                        tutors = parsed;
                    }
                } catch (error) {
                    tutors = [];
                }
            } else {
                tutors = @json($tutoresIniciales);
            }

            ensurePrincipal();
            renderTutors();
            fillBreeds();
            syncBreedSelection();
        });
    </script>
@endpush
@endsection
