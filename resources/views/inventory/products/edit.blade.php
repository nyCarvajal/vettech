@extends('layouts.app')
@section('content')
@php
    $productsForJs = ($costProducts ?? collect())->map(fn($p) => ['id' => (int) $p->id, 'name' => $p->name, 'cost_avg' => (float) ($p->cost_avg ?? 0)])->values();
@endphp
<div class="container" x-data="productServiceForm({rows: @js(old('cost_structure', $product->cost_structure ?? [])), products: @js($productsForJs), salePrice: {{ (float) old('sale_price', $product->sale_price) }}, commissionPercent: {{ (float) old('cost_structure_commission_percent', $product->cost_structure_commission_percent ?? 0) }}})">
    <h1 class="text-xl font-bold mb-4">Editar producto</h1>
    <form method="post" action="{{ route('products.update', $product) }}" class="space-y-2">
        @csrf
        @method('PUT')
        <input name="name" value="{{ $product->name }}" class="border p-2 w-full" required>
        <select name="type" x-model="type" class="border p-2 w-full">
            @foreach(['med'=>'Medicamento','insumo'=>'Insumo','alimento'=>'Alimento','servicio'=>'Servicio'] as $key=>$label)
                <option value="{{ $key }}" @selected(old('type', $product->type)==$key)>{{ $label }}</option>
            @endforeach
        </select>
        <input name="unit" value="{{ $product->unit }}" class="border p-2 w-full" required>
        <label class="block"><input type="checkbox" name="requires_batch" value="1" @checked($product->requires_batch)> Requiere lote</label>
        <input type="number" name="min_stock" value="{{ $product->min_stock }}" class="border p-2 w-full" required>
        <input type="number" step="0.01" name="sale_price" x-model.number="salePrice" value="{{ $product->sale_price }}" class="border p-2 w-full" required>

        <div class="border rounded p-3 space-y-3">
            <p class="text-sm text-gray-700" x-show="type !== 'servicio'">Esta sección aplica para tipo <strong>Servicio</strong>.</p>
            <div class="grid md:grid-cols-2 gap-3">
                <input type="number" name="estimated_duration_minutes" :disabled="type !== 'servicio'" value="{{ old('estimated_duration_minutes', $product->estimated_duration_minutes) }}" min="1" class="border p-2 w-full" placeholder="Tiempo estimado (min)">
                @php($roles = old('authorized_roles', $product->authorized_roles ?? []))
                <select name="authorized_roles[]" multiple :disabled="type !== 'servicio'" class="border p-2 w-full">
                    @foreach (['Administrador', 'Veterinario', 'Auxiliar', 'Groomer', 'Bañador'] as $role)
                        <option value="{{ $role }}" @selected(in_array($role, $roles, true))>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <strong>Estructura de costos (opcional)</strong>
                    <button type="button" @click="addRow()" :disabled="type !== 'servicio'" class="btn btn-sm btn-primary">Agregar producto</button>
                </div>
                <div class="overflow-auto">
                    <table class="table table-sm w-full">
                        <thead><tr><th>Producto</th><th>Costo</th><th>Cantidad</th><th>Costo x ml</th><th>Mls proceso</th><th>Costo aplicación</th><th></th></tr></thead>
                        <tbody>
                            <template x-for="(row,index) in rows" :key="index">
                                <tr>
                                    <td><select :name="`cost_structure[${index}][product_id]`" x-model.number="row.product_id" @change="applyProductData(row)" :disabled="type !== 'servicio'" class="form-control"><option value="">Seleccione</option><template x-for="p in products" :key="p.id"><option :value="p.id" x-text="p.name"></option></template></select></td>
                                    <td><input :name="`cost_structure[${index}][unit_cost]`" x-model.number="row.unit_cost" @input="recalculate(row)" :disabled="type !== 'servicio'" type="number" step="0.01" class="form-control"></td>
                                    <td><input :name="`cost_structure[${index}][quantity_available]`" x-model.number="row.quantity_available" @input="recalculate(row)" :disabled="type !== 'servicio'" type="number" step="0.01" class="form-control"></td>
                                    <td><input :value="money(row.cost_per_ml)" readonly class="form-control"></td>
                                    <td><input :name="`cost_structure[${index}][quantity_used]`" x-model.number="row.quantity_used" @input="recalculate(row)" :disabled="type !== 'servicio'" type="number" step="0.01" class="form-control"></td>
                                    <td><input :name="`cost_structure[${index}][application_cost]`" x-model.number="row.application_cost" readonly class="form-control"></td>
                                    <td><button type="button" @click="removeRow(index)" :disabled="type !== 'servicio'">Quitar</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="grid md:grid-cols-2 gap-3 mt-2">
                    <input type="number" name="cost_structure_commission_percent" :disabled="type !== 'servicio'" x-model.number="commissionPercent" min="0" max="100" step="0.01" class="border p-2 w-full" placeholder="Comisión colaborador (%)">
                    <div class="text-sm">
                        <div>Costo parcial: <strong x-text="money(partialCost())"></strong></div>
                        <div>Ingreso colaborador: <strong x-text="money(collaboratorIncome())"></strong></div>
                        <div>Costo total: <strong x-text="money(totalCost())"></strong></div>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-primary">Actualizar</button>
    </form>
</div>
<script>
function productServiceForm(config){
 const row=()=>({product_id:'',unit_cost:0,quantity_available:0,quantity_used:0,cost_per_ml:0,application_cost:0});
 return {type:'{{ old('type', $product->type) }}', rows:(config.rows||[]).map(r=>({...row(),...r})), products:config.products||[], salePrice:config.salePrice||0, commissionPercent:config.commissionPercent||0,
 addRow(){this.rows.push(row())}, removeRow(i){this.rows.splice(i,1)}, money(v){return Number(v||0).toLocaleString('es-CO',{minimumFractionDigits:2,maximumFractionDigits:2})},
 applyProductData(r){const p=this.products.find(x=>x.id===Number(r.product_id)); if(!p) return; r.unit_cost=Number(p.cost_avg||0); this.recalculate(r)},
 recalculate(r){const q=Number(r.quantity_available||0); r.cost_per_ml=q>0?Number(r.unit_cost||0)/q:0; r.application_cost=r.cost_per_ml*Number(r.quantity_used||0)},
 partialCost(){return this.rows.reduce((s,r)=>s+Number(r.application_cost||0),0)}, collaboratorIncome(){return Number(this.salePrice||0)*(Number(this.commissionPercent||0)/100)}, totalCost(){return this.partialCost()+this.collaboratorIncome()}
 }
}
</script>
@endsection
