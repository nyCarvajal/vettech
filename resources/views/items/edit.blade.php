@extends('layouts.app')

@section('content')
@php
    $typeValue = old('type', $item->type ?? 'product');
    $inventariableValue = old('inventariable', $item->inventariable) ? 'true' : 'false';
    $trackValue = old('track_inventory', $item->track_inventory) ? 'true' : 'false';
    $costProductsForJs = $costProducts->map(fn ($product) => [
        'id' => (int) $product->id,
        'name' => $product->nombre,
        'cost_price' => (float) ($product->cost_price ?? 0),
        'stock' => (float) ($product->stock ?? 0),
    ])->values();
@endphp
<div class="mx-auto w-full max-w-6xl px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Editar ítem</h1>
            <p class="text-sm text-slate-500">Actualiza la información del producto o servicio.</p>
        </div>
        <a href="{{ route('items.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Volver</a>
    </div>

    <form method="POST" action="{{ route('items.update', $item) }}" class="space-y-6"
        x-data="serviceForm({
            type: '{{ $typeValue }}',
            inventariable: {{ $inventariableValue }},
            trackInventory: {{ $trackValue }},
            salePrice: {{ (float) old('sale_price', $item->sale_price ?? 0) }},
            costPrice: {{ (float) old('cost_price', $item->cost_price ?? 0) }},
            commissionPercent: {{ (float) old('cost_structure_commission_percent', $item->cost_structure_commission_percent ?? 0) }},
            rows: @js(old('cost_structure', $item->cost_structure ?? [])),
            products: @js($costProductsForJs),
        })"
        x-effect="if (type === 'service') { inventariable = false; trackInventory = false; }">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Información básica</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <input type="text" name="nombre" value="{{ old('nombre', $item->nombre) }}" required class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Nombre">
                <input type="text" name="sku" value="{{ old('sku', $item->sku) }}" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="SKU">
                <select name="tipo" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    <option value="">Selecciona una categoría</option>
                    @foreach ($categoryOptions as $option)
                        <option value="{{ $option }}" @selected((string) old('tipo', $item->tipo) === (string) $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <select name="area" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    <option value="">Selecciona un área</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" @selected((string) old('area', $item->area) === (string) $area->id)>{{ $area->descripcion }}</option>
                    @endforeach
                </select>
                <select name="type" x-model="type" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    <option value="product">Producto</option>
                    <option value="service">Servicio</option>
                </select>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Precios</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <input type="number" name="sale_price" x-model.number="salePrice" step="0.01" min="0" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Precio de venta">
                <input type="number" name="cost_price" x-model.number="costPrice" step="0.01" min="0" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Costo">
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            <p class="rounded-md bg-amber-50 px-3 py-2 text-xs text-amber-700" x-show="type !== 'service'">Esta sección es opcional y solo aplica cuando el tipo es <strong>Servicio</strong>. Cambia el tipo arriba para activarla.</p>
            <div class="grid gap-4 md:grid-cols-2">
                <input type="number" name="estimated_duration_minutes" :disabled="type !== 'service'" value="{{ old('estimated_duration_minutes', $item->estimated_duration_minutes) }}" min="1" class="rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Tiempo estimado (min)">
                <select name="authorized_roles[]" multiple :disabled="type !== 'service'" class="rounded-lg border-slate-200 px-3 py-2 text-sm">
                    @php($roles = old('authorized_roles', $item->authorized_roles ?? []))
                    @foreach (['Administrador', 'Veterinario', 'Auxiliar', 'Groomer', 'Bañador'] as $role)
                        <option value="{{ $role }}" @selected(in_array($role, $roles, true))>{{ $role }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-800">Estructura de costos (opcional)</h3>
                        <p class="text-xs text-slate-500">Agrega varios productos/insumos usados en el servicio para calcular el costo real.</p>
                    </div>
                    <button type="button" @click="addRow()" :disabled="type !== 'service'" class="rounded bg-mint-600 disabled:opacity-50 px-3 py-1 text-xs font-semibold text-white">Agregar producto</button>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-700"><tr><th class="p-2 text-left">Producto</th><th class="p-2 text-left">Costo producto</th><th class="p-2 text-left">Cantidad</th><th class="p-2 text-left">Costo x ml</th><th class="p-2 text-left">Mls proceso</th><th class="p-2 text-left">Costo aplicación</th><th></th></tr></thead>
                        <tbody>
                        <template x-for="(row, index) in rows" :key="row._key">
                            <tr class="border-t border-slate-100">
                                <td class="p-2"><select :name="`cost_structure[${index}][item_id]`" x-model.number="row.item_id" @change="applyProductData(row)" :disabled="type !== 'service'" class="w-full rounded border-slate-200 text-sm"><option value="">Selecciona</option><template x-for="product in products" :key="product.id"><option :value="product.id" x-text="product.name"></option></template></select></td>
                                <td class="p-2"><input :name="`cost_structure[${index}][unit_cost]`" x-model.number="row.unit_cost" @input="recalculate(row)" :disabled="type !== 'service'" type="number" step="0.01" min="0" class="w-full rounded border-slate-200"></td>
                                <td class="p-2"><input :name="`cost_structure[${index}][quantity_available]`" x-model.number="row.quantity_available" @input="recalculate(row)" :disabled="type !== 'service'" type="number" step="0.01" min="0" class="w-full rounded border-slate-200"></td>
                                <td class="p-2"><input :value="money(row.cost_per_ml)" readonly class="w-full rounded border-slate-200 bg-slate-50"></td>
                                <td class="p-2"><input :name="`cost_structure[${index}][quantity_used]`" x-model.number="row.quantity_used" @input="recalculate(row)" :disabled="type !== 'service'" type="number" step="0.01" min="0" class="w-full rounded border-slate-200"></td>
                                <td class="p-2"><input :name="`cost_structure[${index}][application_cost]`" x-model.number="row.application_cost" readonly class="w-full rounded border-slate-200 bg-slate-50"></td>
                                <td class="p-2"><button type="button" @click="removeRow(index)" :disabled="type !== 'service'" class="text-xs text-red-600 disabled:opacity-50">Quitar</button></td>
                            </tr>
                        </template>
                        </tbody>
                        <tfoot class="bg-slate-50">
                            <tr>
                                <td colspan="5" class="p-2 text-right text-xs font-semibold text-slate-600">Total insumos</td>
                                <td class="p-2 text-sm font-semibold text-slate-800" x-text="money(partialCost())"></td>
                                <td class="p-2 text-xs text-slate-500" x-text="`${rows.length} producto(s)`"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="grid gap-3 text-sm md:grid-cols-2">
                <input type="number" name="cost_structure_commission_percent" :disabled="type !== 'service'" x-model.number="commissionPercent" min="0" max="100" step="0.01" class="rounded-lg border-slate-200 px-3 py-2" placeholder="Comisión colaborador (%)">
                <div class="space-y-1 rounded-lg bg-slate-50 p-3">
                    <p>Costo parcial: <span class="font-semibold" x-text="money(partialCost())"></span></p>
                    <p>Ingreso colaborador: <span class="font-semibold" x-text="money(collaboratorIncome())"></span></p>
                    <p>Costo total: <span class="font-semibold text-red-600" x-text="money(totalCost())"></span></p>
                    <p>Utilidad bruta: <span class="font-semibold text-indigo-600" x-text="money(grossProfit())"></span></p>
                    <p>Margen neto: <span class="font-semibold text-green-600" x-text="`${netMargin().toFixed(2)}%`"></span></p>
                    <button type="button" class="rounded bg-slate-200 px-2 py-1 text-xs disabled:opacity-50" @click="costPrice = totalCost()" :disabled="type !== 'service'">Usar costo total calculado</button>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Inventario</h2>
            <div class="mt-4 space-y-4">
                <div class="flex items-center gap-4">
                    <input type="hidden" name="inventariable" value="0">
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="inventariable" value="1" x-model="inventariable" :disabled="type === 'service'" class="rounded border-slate-300 text-mint-600"> Inventariable</label>
                    <input type="hidden" name="track_inventory" value="0">
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" name="track_inventory" value="1" x-model="trackInventory" :disabled="type === 'service'" class="rounded border-slate-300 text-mint-600"> Controlar stock</label>
                </div>
                <div x-show="inventariable || trackInventory" x-cloak class="grid gap-4 md:grid-cols-2">
                    <input type="number" name="stock" step="0.001" min="0" value="{{ old('stock', $item->stock) }}" class="rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Stock actual">
                    <input type="number" name="cantidad" step="0.001" min="0" value="{{ old('cantidad', $item->cantidad) }}" class="rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Stock mínimo">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('items.index') }}" class="rounded-lg border border-violet-200 bg-violet-50 px-4 py-2 text-sm font-semibold text-violet-700 hover:bg-violet-100">Cancelar</a>
            <button type="submit" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-violet-700">Guardar cambios</button>
        </div>
    </form>
</div>

<script>
function serviceForm(config) {
    const defaultRow = () => ({ _key: `${Date.now()}-${Math.random()}`, item_id: '', unit_cost: 0, quantity_available: 0, quantity_used: 0, cost_per_ml: 0, application_cost: 0 });
    const normalizedRows = (config.rows || []).map((row) => ({ ...defaultRow(), ...row, _key: row?._key || `${Date.now()}-${Math.random()}` }));
    if (normalizedRows.length === 0) {
        normalizedRows.push(defaultRow());
    }
    return {
        ...config,
        rows: normalizedRows,
        products: config.products || [],
        init() {
            this.rows.forEach((row) => this.recalculate(row));
        },
        addRow() { this.rows.push(defaultRow()); },
        removeRow(index) {
            this.rows.splice(index, 1);
            if (this.rows.length === 0) {
                this.rows.push(defaultRow());
            }
        },
        money(value) { return Number(value || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
        applyProductData(row) {
            const product = this.products.find((item) => item.id === Number(row.item_id));
            if (!product) return;
            row.unit_cost = Number(product.cost_price || 0);
            row.quantity_available = Number(product.stock || 0);
            this.recalculate(row);
        },
        recalculate(row) {
            const quantity = Number(row.quantity_available || 0);
            row.cost_per_ml = quantity > 0 ? Number(row.unit_cost || 0) / quantity : 0;
            row.application_cost = row.cost_per_ml * Number(row.quantity_used || 0);
        },
        partialCost() { return this.rows.reduce((sum, row) => sum + Number(row.application_cost || 0), 0); },
        collaboratorIncome() { return Number(this.salePrice || 0) * (Number(this.commissionPercent || 0) / 100); },
        totalCost() { return this.partialCost() + this.collaboratorIncome(); },
        grossProfit() { return Number(this.salePrice || 0) - this.totalCost(); },
        netMargin() { return Number(this.salePrice || 0) > 0 ? (this.grossProfit() / Number(this.salePrice)) * 100 : 0; },
    }
}
</script>
@endsection
