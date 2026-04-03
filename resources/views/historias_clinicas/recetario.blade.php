@extends('layouts.app')

@section('content')
<div class="container py-4">
    @php
        $isEditing = isset($prescription) && $prescription;
        $itemsFromForm = old('items', $formItems ?? []);
        $items = collect($itemsFromForm)->filter(fn ($item) => is_array($item))->values();
        if ($items->isEmpty()) {
            $items = collect([[]]);
        }
    @endphp

    <div class="d-flex align-items-center mb-3">
        <div class="flex-grow-1">
            @if ($historia)
                <p class="text-muted mb-1">Historia clínica #{{ $historia->id }}</p>
                <h1 class="h4 mb-0">{{ $isEditing ? 'Editar recetario' : 'Nuevo recetario' }}</h1>
            @else
                <p class="text-muted mb-1">Selecciona el paciente</p>
                <h1 class="h4 mb-0">{{ $isEditing ? 'Editar recetario' : 'Nuevo recetario' }}</h1>
            @endif
        </div>
        @if ($historia)
            <span class="badge bg-light text-dark px-3 py-2">Paciente: {{ optional($historia->paciente)->nombres }} {{ optional($historia->paciente)->apellidos }}</span>
        @endif
    </div>

    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Tip:</strong> Puedes agregar medicamentos manualmente. Estos aparecerán en el recetario pero no serán facturables.
        </div>
    </div>

    <form method="post" action="{{ $isEditing ? route('historias-clinicas.recetarios.update', $prescription) : ($historia ? route('historias-clinicas.recetarios.store', $historia) : route('historias-clinicas.recetarios.quick.store')) }}">
        @csrf
        @if($isEditing)
            @method('PUT')
        @endif

        @if (! $historia)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <label class="form-label">Paciente</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">Busca y selecciona un paciente</option>
                        @foreach(($pacientes ?? collect()) as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('patient_id') == $paciente->id)>
                                {{ $paciente->display_name }}
                                · Raza: {{ optional($paciente->breed)->name ?? 'Sin raza' }}
                                · Tutor: {{ optional($paciente->owner)->name ?? 'Sin tutor' }} ({{ optional($paciente->owner)->document ?? 'N/D' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div id="items-wrapper">
            @foreach($items as $index => $item)
                @php($isManual = (bool) ($item['is_manual'] ?? false))
                <div class="card shadow-sm mb-3 prescription-item">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary" data-item-label>Medicamento #{{ $index + 1 }}</span>
                            <span class="text-muted small">Facturable si es un producto del inventario</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-manual" type="checkbox" name="items[{{ $index }}][is_manual]" value="1" id="manualToggle{{ $index }}" @checked($isManual)>
                            <label class="form-check-label" for="manualToggle{{ $index }}">Ingresar manual</label>
                        </div>
                    </div>
                    <div class="card-body row g-3 align-items-end">
                        <div class="col-md-5 manual-hidden {{ $isManual ? 'd-none' : '' }}">
                            <label class="form-label">Producto del inventario</label>
                            <select name="items[{{ $index }}][product_id]" class="form-select" data-product-select>
                                <option value="">Seleccione</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected(($item['product_id'] ?? null) == $product->id)>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 manual-visible {{ $isManual ? '' : 'd-none' }}">
                            <label class="form-label">Nombre del medicamento (manual)</label>
                            <input type="text" name="items[{{ $index }}][manual_name]" class="form-control" placeholder="Ej: Amoxicilina 500mg" value="{{ $item['manual_name'] ?? '' }}">
                            <small class="text-muted">No se facturará automáticamente.</small>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" step="0.01" min="1" name="items[{{ $index }}][qty_requested]" class="form-control" required value="{{ $item['qty_requested'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dosis</label>
                            <input type="text" name="items[{{ $index }}][dose]" class="form-control" placeholder="Ej: 1 tableta" value="{{ $item['dose'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Frecuencia</label>
                            <input type="text" name="items[{{ $index }}][frequency]" class="form-control" placeholder="Cada 8 horas" value="{{ $item['frequency'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Días</label>
                            <input type="number" min="1" name="items[{{ $index }}][duration_days]" class="form-control" placeholder="Ej: 5" value="{{ $item['duration_days'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Indicaciones</label>
                            <textarea name="items[{{ $index }}][instructions]" class="form-control" rows="2" placeholder="Recomendaciones adicionales">{{ $item['instructions'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <label class="form-label" for="observations">Observaciones generales</label>
                <textarea id="observations" name="observations" class="form-control" rows="3" placeholder="Ej: recomendaciones generales para el tratamiento">{{ old('observations', $prescription->observations ?? null) }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary" id="add-row"><i class="bi bi-plus-lg me-1"></i>Agregar otro medicamento</button>
            <div class="d-flex gap-2">
                @if ($historia)
                    <a href="{{ route('historias-clinicas.show', $historia) }}" class="btn btn-link">Cancelar</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-link">Cancelar</a>
                @endif
                <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Actualizar recetario' : 'Guardar recetario' }}</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const wrapper = document.getElementById('items-wrapper');

    const refreshLabels = () => {
        wrapper.querySelectorAll('[data-item-label]').forEach((label, idx) => {
            label.textContent = `Medicamento #${idx + 1}`;
        });
    };

    const toggleSections = (card, isManual) => {
        card.querySelector('.manual-hidden').classList.toggle('d-none', isManual);
        card.querySelector('.manual-visible').classList.toggle('d-none', !isManual);
    };

    const bindToggle = (card) => {
        const toggle = card.querySelector('.toggle-manual');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('change', (e) => {
            toggleSections(card, e.target.checked);
        });

        toggleSections(card, toggle.checked);
    };

    const reindex = () => {
        wrapper.querySelectorAll('.prescription-item').forEach((card, idx) => {
            card.querySelectorAll('select, input, textarea').forEach(el => {
                const name = el.getAttribute('name');
                if (! name) return;
                el.setAttribute('name', name.replace(/items\[[0-9]+\]/, `items[${idx}]`));
                if (el.id && el.id.startsWith('manualToggle')) {
                    const label = card.querySelector(`label[for="${el.id}"]`);
                    el.id = `manualToggle${idx}`;
                    label?.setAttribute('for', el.id);
                }
            });
        });
        refreshLabels();
    };

    wrapper.querySelectorAll('.prescription-item').forEach(bindToggle);
    refreshLabels();

    document.getElementById('add-row').addEventListener('click', () => {
        const template = wrapper.querySelector('.prescription-item');
        const clone = template.cloneNode(true);

        clone.querySelectorAll('select, input, textarea').forEach(el => {
            if (el.type === 'checkbox') {
                el.checked = false;
            } else if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            } else {
                el.value = '';
            }
        });

        wrapper.appendChild(clone);
        bindToggle(clone);
        toggleSections(clone, false);
        reindex();
    });
</script>
@endpush
