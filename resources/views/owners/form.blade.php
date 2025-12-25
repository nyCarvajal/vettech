@extends('layouts.app')

@section('content')
@php
    $documentTypes = $documentTypes ?? collect();
    $whatsappValue = old('whatsapp', $owner->whatsapp);
    $selectedPrefix = old('whatsapp_prefix', '+57');
    $whatsappNumber = old('whatsapp_number');

    if ($whatsappValue && ! $whatsappNumber && preg_match('/^(\+\d{1,3})\s*(.*)$/', $whatsappValue, $matches)) {
        $selectedPrefix = $matches[1];
        $whatsappNumber = trim($matches[2]);
    }

    $selectedDocumentTypeId = old('document_type_id');
    if (! $selectedDocumentTypeId && $owner->document_type) {
        $selectedDocumentTypeId = optional($documentTypes->firstWhere('tipo', $owner->document_type))->id;
    }

    $departamentos = $departamentos ?? collect();
    $municipios = $municipios ?? collect();
    $selectedDepartamentoId = old('departamento_id', $owner->departamento_id ?: optional($owner->municipio)->departamento_id);
    $selectedMunicipioId = old('municipio_id', $owner->municipio_id);
@endphp

<style>
    .contact-block {
        background: linear-gradient(135deg, #f3e8ff 0%, #d1fae5 100%);
        border: 1px solid #d6bcfa;
    }

    .accent-badge {
        background-color: #8b5cf6;
        color: #ffffff;
        font-weight: 600;
    }

    .mint-border {
        border-color: #34d399 !important;
    }
</style>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $owner->exists ? 'Editar tutor' : 'Nuevo tutor' }}</h1>
            <p class="text-muted mb-0">Completa los datos de contacto del tutor.</p>
        </div>
        <a href="{{ route('owners.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post" action="{{ $owner->exists ? route('owners.update', $owner) : route('owners.store') }}">
                @csrf
                @if($owner->exists)
                    @method('PUT')
                @endif
                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" value="{{ old('name', $owner->name) }}" class="form-control" required>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-3 rounded-3 border mint-border h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge accent-badge">Documento</span>
                                <span class="text-muted small">Selecciona el tipo y número</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label mb-1">Tipo de documento</label>
                                    <select name="document_type_id" class="form-select">
                                        <option value="">Selecciona</option>
                                        @foreach($documentTypes as $documentType)
                                            <option value="{{ $documentType->id }}" {{ (int)$selectedDocumentTypeId === (int)$documentType->id ? 'selected' : '' }}>
                                                {{ $documentType->tipo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mb-1">Número</label>
                                    <input type="text" name="document" value="{{ old('document', $owner->document) }}" class="form-control" placeholder="Ingrese el número">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="contact-block rounded-3 p-3 shadow-sm">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge accent-badge">Información de contacto</span>
                                <span class="text-muted">Centraliza correo, WhatsApp y teléfono.</span>
                            </div>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="phone" value="{{ old('phone', $owner->phone) }}" class="form-control" placeholder="Ej. 3216549870">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $owner->email) }}" class="form-control" placeholder="tutor@correo.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">WhatsApp</label>
                                    <div class="input-group">
                                        <select name="whatsapp_prefix" class="form-select" style="max-width: 130px;">
                                            <option value="+57" {{ $selectedPrefix === '+57' ? 'selected' : '' }}>🇨🇴 +57</option>
                                            <option value="+1" {{ $selectedPrefix === '+1' ? 'selected' : '' }}>🇺🇸 +1</option>
                                            <option value="+52" {{ $selectedPrefix === '+52' ? 'selected' : '' }}>🇲🇽 +52</option>
                                            <option value="+54" {{ $selectedPrefix === '+54' ? 'selected' : '' }}>🇦🇷 +54</option>
                                            <option value="+51" {{ $selectedPrefix === '+51' ? 'selected' : '' }}>🇵🇪 +51</option>
                                        </select>
                                        <input type="text" name="whatsapp_number" value="{{ $whatsappNumber }}" class="form-control" placeholder="Número de WhatsApp">
                                    </div>
                                    <div class="form-text">El prefijo se guardará junto al número.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address" value="{{ old('address', $owner->address) }}" class="form-control" placeholder="Calle, número y ciudad">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Departamento</label>
                        <select name="departamento_id" id="departamento_id" class="form-select">
                            <option value="">Selecciona</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->id }}" {{ (int) $selectedDepartamentoId === (int) $departamento->id ? 'selected' : '' }}>
                                    {{ $departamento->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Municipio</label>
                        <select name="municipio_id" id="municipio_id" class="form-select">
                            <option value="">Selecciona un departamento</option>
                        </select>
                        <div class="form-text">Opcional, se ajusta según el departamento.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Preferencias, recordatorios, etc.">{{ old('notes', $owner->notes) }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const departamentos = @json($departamentos->map(function ($dep) {
            return [
                'id' => $dep->id,
                'nombre' => $dep->nombre,
            ];
        }));

        const municipios = @json($municipios->map(function ($mun) {
            return [
                'id' => $mun->id,
                'nombre' => $mun->nombre,
                'departamento_id' => $mun->departamento_id,
            ];
        }));

        const departamentoSelect = document.getElementById('departamento_id');
        const municipioSelect = document.getElementById('municipio_id');
        let selectedMunicipioId = '{{ $selectedMunicipioId }}';

        function renderMunicipios() {
            const departamentoId = departamentoSelect.value;
            municipioSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = departamentoId ? 'Selecciona un municipio' : 'Selecciona un departamento';
            municipioSelect.appendChild(placeholder);

            if (!departamentoId) {
                return;
            }

            municipios
                .filter((mun) => String(mun.departamento_id) === departamentoId)
                .forEach((mun) => {
                    const option = document.createElement('option');
                    option.value = mun.id;
                    option.textContent = mun.nombre;
                    if (String(mun.id) === selectedMunicipioId) {
                        option.selected = true;
                    }
                    municipioSelect.appendChild(option);
                });
        }

        departamentoSelect.addEventListener('change', () => {
            selectedMunicipioId = '';
            renderMunicipios();
        });

        municipioSelect.addEventListener('change', () => {
            selectedMunicipioId = municipioSelect.value;
        });

        renderMunicipios();
    });
</script>
@endsection
