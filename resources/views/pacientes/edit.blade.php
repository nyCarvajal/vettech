@extends('layouts.vertical', ['subtitle' => 'Pacientes'])

@section('content')
    @include('layouts.partials/page-title', ['title' => 'Pacientes', 'subtitle' => 'Editar'])

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pacientes.update', $paciente) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="tipo_documento_id">Tipo de documento</label>
                        @php
                            $tipoDocumentoSeleccionado = old('tipo_documento_id', optional($tiposDocumento->firstWhere('tipo', $paciente->tipo_documento))->id);
                        @endphp
                        <select name="tipo_documento_id" id="tipo_documento_id" class="form-select">
                            <option value="" disabled {{ $tipoDocumentoSeleccionado ? '' : 'selected' }}>Seleccione un tipo</option>
                            @foreach ($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" @selected($tipoDocumentoSeleccionado == $tipo->id)>
                                    {{ $tipo->tipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="numero_documento">Número de documento</label>
                        <input type="text" name="numero_documento" id="numero_documento" class="form-control"
                            value="{{ old('numero_documento', $paciente->numero_documento) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nombres">Nombres</label>
                        <input type="text" name="nombres" id="nombres" class="form-control" value="{{ old('nombres', $paciente->nombres) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" value="{{ old('apellidos', $paciente->apellidos) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control"
                            value="{{ old('direccion', $paciente->direccion) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="departamento_id">Departamento</label>
                        @php
                            $municipioSeleccionado = old('municipio_id', $selectedMunicipioId);
                            $departamentoSeleccionado = old('departamento_id', optional($municipios->firstWhere('id', $municipioSeleccionado))->departamento_id);
                        @endphp
                        <select name="departamento_id" id="departamento_id" class="form-select">
                            <option value="" disabled {{ $departamentoSeleccionado ? '' : 'selected' }}>Seleccione un departamento</option>
                            @foreach ($departamentos as $departamento)
                                <option value="{{ $departamento->id }}" @selected($departamentoSeleccionado == $departamento->id)>
                                    {{ $departamento->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="municipio_id">Ciudad</label>
                        <select name="municipio_id" id="municipio_id" class="form-select">
                            <option value="" disabled {{ $municipioSeleccionado ? '' : 'selected' }}>Seleccione un municipio</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="whatsapp">WhatsApp</label>
                        <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp', $paciente->whatsapp) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Correo</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $paciente->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="sexo">Sexo</label>
                        <select name="sexo" id="sexo" class="form-select">
                            <option value="">Selecciona una opción</option>
                            <option value="hombre" @selected(old('sexo', $paciente->sexo) === 'hombre')>Hombre</option>
                            <option value="mujer" @selected(old('sexo', $paciente->sexo) === 'mujer')>Mujer</option>
                            <option value="otro" @selected(old('sexo', $paciente->sexo) === 'otro')>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento ? \Illuminate\Support\Carbon::parse($paciente->fecha_nacimiento)->format('Y-m-d') : null) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="acompanante">Acompañante</label>
                        <input type="text" name="acompanante" id="acompanante" class="form-control"
                            value="{{ old('acompanante', $paciente->acompanante) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="acompanante_contacto">Contacto del acompañante</label>
                        <input type="text" name="acompanante_contacto" id="acompanante_contacto" class="form-control"
                            value="{{ old('acompanante_contacto', $paciente->acompanante_contacto) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="alergias">Alergias</label>
                        <textarea name="alergias" id="alergias" rows="2" class="form-control">{{ old('alergias', $paciente->alergias) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="alergias">Alergias</label>
                        <textarea name="alergias" id="alergias" rows="2" class="form-control">{{ old('alergias', $paciente->alergias) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="observaciones">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3" class="form-control">{{ old('observaciones', $paciente->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-link">Cancelar</a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary">Listado</a>
                        <button type="submit" class="btn btn-success">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const municipios = @json($municipios);
            const depSelect = document.getElementById('departamento_id');
            const muniSelect = document.getElementById('municipio_id');
            const selectedMunicipio = {{ $municipioSeleccionado ?? 'null' }};

            function loadCities() {
                const depId = depSelect.value;
                const currentMunicipio = muniSelect.value || selectedMunicipio;
                muniSelect.innerHTML = '<option value="" disabled selected>Seleccione un municipio</option>';

                municipios
                    .filter((municipio) => municipio.departamento_id == depId)
                    .forEach((municipio) => {
                        const option = document.createElement('option');
                        option.value = municipio.id;
                        option.text = municipio.nombre;
                        if (municipio.id == currentMunicipio) {
                            option.selected = true;
                        }
                        muniSelect.appendChild(option);
                    });
            }

            depSelect.addEventListener('change', loadCities);

            if (depSelect.value) {
                loadCities();
            }
        });
    </script>
@endsection
