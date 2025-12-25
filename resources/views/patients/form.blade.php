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
                                    <h6 class="mb-0 fw-semibold text-purple">Selecciona el tutor</h6>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-muted">Buscar por nombre o cédula</label>
                                <div class="input-group">
                                <span class="input-group-text input-icon-mint">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z"/><path d="M15.854 15.146 11.5 10.793" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </span>
                                    <input type="search" id="ownerSearch" class="form-control" placeholder="Ej: Laura Gómez o 123456" aria-label="Buscar tutor">
                                </div>
                            </div>
                            <label class="form-label text-muted">Tutor asignado</label>
                            <select name="owner_id" id="ownerSelect" class="form-select fancy-select" required>
                                <option value="">Selecciona un tutor</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" data-search="{{ strtolower($owner->name . ' ' . ($owner->document ?? '')) }}" @selected(old('owner_id', $patient->owner_id) == $owner->id)>
                                        {{ $owner->name }} — {{ $owner->document ? 'CC ' . $owner->document : 'Sin documento' }}
                                    </option>
                                @endforeach
                            </select>
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
                                        <select name="breed_id" class="form-select fancy-select">
                                            <option value="">Selecciona raza</option>
                                            @foreach($breeds as $breed)
                                                <option value="{{ $breed->id }}" @selected(old('breed_id', $patient->breed_id) == $breed->id)>{{ $breed->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('ownerSearch');
            const ownerSelect = document.getElementById('ownerSelect');

            searchInput?.addEventListener('input', (event) => {
                const term = event.target.value.toLowerCase();
                Array.from(ownerSelect.options).forEach(option => {
                    if (!option.value) return; // skip placeholder
                    const matches = option.dataset.search?.includes(term);
                    option.hidden = term && !matches;
                });
            });
        });
    </script>
@endpush
@endsection
