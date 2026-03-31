@extends('layouts.app', ['subtitle' => 'Nueva factura de compra'])
@section('content')
<div class="container-fluid">
<form method="POST" action="{{ route('supplier-invoices.store') }}" id="invoice-form">@csrf
<div class="card mb-3"><div class="card-body row g-3">
    <div class="col-md-4"><label class="form-label">Proveedor *</label><select name="supplier_id" class="form-select" required><option value="">Seleccione...</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->razon_social }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label"># Factura *</label><input name="numero_factura" class="form-control" required></div>
    <div class="col-md-2"><label class="form-label">Fecha *</label><input type="date" name="fecha_factura" value="{{ now()->toDateString() }}" class="form-control" required></div>
    <div class="col-md-2"><label class="form-label">Vencimiento</label><input type="date" name="fecha_vencimiento" class="form-control"></div>
    <div class="col-md-2"><label class="form-label">Estado</label><select class="form-select" name="estado"><option value="borrador">Borrador</option><option value="confirmada">Confirmada</option></select></div>
    <div class="col-md-2"><label class="form-label">Descuento</label><input type="number" step="0.01" name="descuento" id="descuento" value="0" class="form-control"></div>
    <div class="col-md-2"><label class="form-label">Impuestos</label><input type="number" step="0.01" name="impuestos" id="impuestos" value="0" class="form-control"></div>
    <div class="col-md-8"><label class="form-label">Observaciones</label><input name="observaciones" class="form-control"></div>
</div></div>

<div class="card"><div class="card-body">
    <div class="d-flex justify-content-between mb-2"><h5>Detalle</h5><div><button type="button" id="add-line" class="btn btn-sm btn-outline-primary">Agregar línea</button> <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#newItemModal">Nuevo producto</button></div></div>
    <div class="table-responsive"><table class="table" id="detail-table"><thead><tr><th>Producto</th><th>Cantidad</th><th>Costo</th><th>Precio venta</th><th>Obsequio</th><th>Subtotal</th><th></th></tr></thead><tbody></tbody></table></div>
    <div class="text-end">
        <p>Subtotal: <span id="sum-subtotal">0.00</span></p>
        <p>Total: <strong id="sum-total">0.00</strong></p>
    </div>
    <button class="btn btn-primary">Guardar factura</button>
</div></div>
</form>
</div>

<div class="modal fade" id="newItemModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Nuevo producto</h5></div><div class="modal-body">
    <div class="mb-2"><input class="form-control" id="new-nombre" placeholder="Nombre"></div>
    <div class="mb-2"><input class="form-control" id="new-sku" placeholder="SKU"></div>
    <div class="row g-2"><div class="col"><input type="number" step="0.01" class="form-control" id="new-cost" placeholder="Costo"></div><div class="col"><input type="number" step="0.01" class="form-control" id="new-sale" placeholder="Venta"></div></div>
</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="button" class="btn btn-success" id="save-item">Crear</button></div></div></div></div>

@php
    $catalogItems = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'nombre' => $item->nombre,
            'cost' => $item->cost_price ?? $item->costo ?? 0,
            'sale' => $item->sale_price ?? $item->valor ?? 0,
        ];
    })->values();
@endphp

<script>
const catalog = @json($catalogItems);
const tbody = document.querySelector('#detail-table tbody');
function options(){return catalog.map(i=>`<option value="${i.id}">${i.nombre}</option>`).join('')}
function recalc(){let subtotal=0;document.querySelectorAll('#detail-table tbody tr').forEach(tr=>{const q=parseFloat(tr.querySelector('.qty').value||0);const c=parseFloat(tr.querySelector('.cost').value||0);const gift=tr.querySelector('.gift').checked;const line=gift?0:q*c;tr.querySelector('.line-subtotal').textContent=line.toFixed(2);if(gift){tr.classList.add('table-warning')} else {tr.classList.remove('table-warning')}subtotal+=line;});const d=parseFloat(document.querySelector('#descuento').value||0);const i=parseFloat(document.querySelector('#impuestos').value||0);document.querySelector('#sum-subtotal').textContent=subtotal.toFixed(2);document.querySelector('#sum-total').textContent=Math.max(0,subtotal-d+i).toFixed(2);} 
function addLine(){const idx=tbody.children.length;const tr=document.createElement('tr');tr.innerHTML=`<td><select name="detalles[${idx}][item_id]" class="form-select item">${options()}</select><input type="hidden" name="detalles[${idx}][descripcion]" value=""></td><td><input name="detalles[${idx}][cantidad]" class="form-control qty" type="number" min="0.001" step="0.001" value="1"></td><td><input name="detalles[${idx}][costo_unitario]" class="form-control cost" type="number" min="0" step="0.01" value="0"></td><td><input name="detalles[${idx}][precio_venta_unitario]" class="form-control sale" type="number" min="0" step="0.01" value="0"></td><td><input type="checkbox" name="detalles[${idx}][es_obsequio]" value="1" class="form-check-input gift"></td><td><span class="line-subtotal">0.00</span></td><td><button type="button" class="btn btn-sm btn-outline-danger remove">x</button></td>`;tbody.appendChild(tr);tr.querySelectorAll('input,select').forEach(e=>e.addEventListener('change', recalc));tr.querySelector('.remove').addEventListener('click',()=>{tr.remove();recalc();});tr.querySelector('.item').addEventListener('change',e=>{const found=catalog.find(c=>String(c.id)===e.target.value);if(found){tr.querySelector('.cost').value=found.cost;tr.querySelector('.sale').value=found.sale;recalc();}});tr.querySelector('.item').dispatchEvent(new Event('change'));}
document.querySelector('#add-line').addEventListener('click', addLine);document.querySelector('#descuento').addEventListener('input', recalc);document.querySelector('#impuestos').addEventListener('input', recalc);addLine();

document.querySelector('#save-item').addEventListener('click', async ()=>{const payload={nombre:document.querySelector('#new-nombre').value,sku:document.querySelector('#new-sku').value,cost_price:document.querySelector('#new-cost').value,sale_price:document.querySelector('#new-sale').value,_token:'{{ csrf_token() }}'};const res=await fetch('{{ route('supplier-items.store') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify(payload)});if(!res.ok){alert('No se pudo crear el producto');return;}const item=await res.json();catalog.push({id:item.id,nombre:item.nombre,cost:item.cost_price||0,sale:item.sale_price||0});document.querySelectorAll('select.item').forEach(s=>s.insertAdjacentHTML('beforeend',`<option value="${item.id}">${item.nombre}</option>`));const modal=bootstrap.Modal.getInstance(document.getElementById('newItemModal'));modal.hide();});
</script>
@endsection
