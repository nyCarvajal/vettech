@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-3">
        <div class="flex-grow-1">
            <p class="text-muted mb-1">Historia clínica #{{ $historia->id }}</p>
            <h1 class="h4 mb-0">Nuevo recetario</h1>
        </div>
        <span class="badge bg-light text-dark px-3 py-2">Paciente: {{ optional($historia->paciente)->nombres }} {{ optional($historia->paciente)->apellidos }}</span>
    </div>

    <div class="alert alert-info d-flex align-items-center gap-2">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Tip:</strong> Puedes agregar medicamentos manualmente. Estos aparecerán en el recetario pero no serán facturables.
        </div>
    </div>

    <form method="post" action="{{ route('historias-clinicas.recetarios.store', $historia) }}">
        @csrf
        <div id="items-wrapper">
            <div class="card shadow-sm mb-3 prescription-item">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary" data-item-label>Medicamento #1</span>
                        <span class="text-muted small">Facturable si es un producto del inventario</span>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-manual" type="checkbox" name="items[0][is_manual]" value="1" id="manualToggle0">
                        <label class="form-check-label" for="manualToggle0">Ingresar manual</label>
                    </div>
                </div>
                <div class="card-body row g-3 align-items-end">
                    <div class="col-md-5 manual-hidden">
                        <label class="form-label">Producto del inventario</label>
                        <select name="items[0][product_id]" class="form-select" data-product-select>
                            <option value="">Seleccione</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 manual-visible d-none">
                        <label class="form-label">Nombre del medicamento (manual)</label>
                        <input type="text" name="items[0][manual_name]" class="form-control" placeholder="Ej: Amoxicilina 500mg">
                        <small class="text-muted">No se facturará automáticamente.</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cantidad</label>
                        <input type="number" step="0.01" min="1" name="items[0][qty_requested]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dosis</label>
                        <input type="text" name="items[0][dose]" class="form-control" placeholder="Ej: 1 tableta">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Frecuencia</label>
                        <input type="text" name="items[0][frequency]" class="form-control" placeholder="Cada 8 horas">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Días</label>
                        <input type="number" min="1" name="items[0][duration_days]" class="form-control" placeholder="Ej: 5">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Indicaciones</label>
                        <textarea name="items[0][instructions]" class="form-control" rows="2" placeholder="Recomendaciones adicionales"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary" id="add-row"><i class="bi bi-plus-lg me-1"></i>Agregar otro medicamento</button>
            <div class="d-flex gap-2">
                <a href="{{ route('historias-clinicas.show', $historia) }}" class="btn btn-link">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar recetario</button>
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
        toggle.addEventListener('change', (e) => {
            toggleSections(card, e.target.checked);
        });
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

    bindToggle(wrapper.querySelector('.prescription-item'));

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
