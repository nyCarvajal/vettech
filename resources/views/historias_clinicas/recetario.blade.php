@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Nuevo recetario para la historia #{{ $historia->id }}</h1>
    <form method="post" action="{{ route('historias-clinicas.recetarios.store', $historia) }}">
        @csrf
        <div id="items-wrapper">
            <div class="card mb-3 prescription-item">
                <div class="card-body row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Producto</label>
                        <select name="items[0][product_id]" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cantidad</label>
                        <input type="number" step="0.01" min="1" name="items[0][qty_requested]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dosis</label>
                        <input type="text" name="items[0][dose]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Frecuencia</label>
                        <input type="text" name="items[0][frequency]" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Días</label>
                        <input type="number" min="1" name="items[0][duration_days]" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <label class="form-label">Indicaciones</label>
                        <textarea name="items[0][instructions]" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary" id="add-row">Agregar otro medicamento</button>
            <div>
                <a href="{{ route('historias-clinicas.show', $historia) }}" class="btn btn-link">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar recetario</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let index = 1;
    document.getElementById('add-row').addEventListener('click', () => {
        const wrapper = document.getElementById('items-wrapper');
        const template = wrapper.querySelector('.prescription-item');
        const clone = template.cloneNode(true);
        clone.querySelectorAll('select, input, textarea').forEach(el => {
            el.name = el.name.replace(/\d+/, index);
            if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            } else {
                el.value = '';
            }
        });
        wrapper.appendChild(clone);
        index++;
    });
</script>
@endpush
