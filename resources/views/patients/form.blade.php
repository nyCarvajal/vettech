@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $patient->exists ? 'Editar paciente' : 'Nuevo paciente' }}</h1>
            <p class="text-muted mb-0">Ficha clínica organizada para pacientes veterinarios.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post" action="{{ $patient->exists ? route('patients.update', $patient) : route('patients.store') }}" enctype="multipart/form-data">
                @csrf
                @if($patient->exists)
                    @method('PUT')
                @endif
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tutor</label>
                        <select name="owner_id" class="form-select" required>
                            <option value="">Selecciona un tutor</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" @selected(old('owner_id', $patient->owner_id) == $owner->id)>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombres" value="{{ old('nombres', $patient->nombres) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $patient->apellidos) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Especie</label>
                        <select name="species_id" class="form-select" required>
                            <option value="">Selecciona especie</option>
                            @foreach($species as $specie)
                                <option value="{{ $specie->id }}" @selected(old('species_id', $patient->species_id) == $specie->id)>{{ $specie->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Raza</label>
                        <select name="breed_id" class="form-select">
                            <option value="">Selecciona raza</option>
                            @foreach($breeds as $breed)
                                <option value="{{ $breed->id }}" @selected(old('breed_id', $patient->breed_id) == $breed->id)>{{ $breed->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sexo</label>
                        <select name="sexo" class="form-select">
                            <option value="">N/D</option>
                            <option value="M" @selected(old('sexo', $patient->sexo) === 'M')>Macho</option>
                            <option value="F" @selected(old('sexo', $patient->sexo) === 'F')>Hembra</option>
                            <option value="NA" @selected(old('sexo', $patient->sexo) === 'NA')>N/A</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($patient->fecha_nacimiento)->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" value="{{ old('color', $patient->color) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Microchip</label>
                        <input type="text" name="microchip" value="{{ old('microchip', $patient->microchip) }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Peso actual (kg)</label>
                        <input type="number" step="0.01" name="peso_actual" value="{{ old('peso_actual', $patient->peso_actual) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Temperamento</label>
                        <select name="temperamento" class="form-select">
                            <option value="">Seleccione</option>
                            @foreach(['tranquilo','nervioso','agresivo','miedoso','otro'] as $temp)
                                <option value="{{ $temp }}" @selected(old('temperamento', $patient->temperamento) === $temp)>{{ ucfirst($temp) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alergias</label>
                        <textarea name="alergias" class="form-control" rows="2">{{ old('alergias', $patient->alergias) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notas</label>
                        <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $patient->observaciones) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $patient->whatsapp) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Foto</label>
                        <input type="file" name="photo" class="form-control">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
