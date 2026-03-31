@extends('layouts.app', ['subtitle' => 'Nuevo pago a proveedor'])
@section('content')
<div class="container-fluid"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('supplier-payments.store') }}">@csrf
<div class="row g-3">
<div class="col-md-4"><label>Proveedor *</label><select class="form-select" name="supplier_id" required>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->razon_social }}</option>@endforeach</select></div>
<div class="col-md-4"><label>Factura (opcional)</label><select class="form-select" name="supplier_invoice_id"><option value="">Sin factura específica</option>@foreach($invoices as $i)<option value="{{ $i->id }}">{{ $i->numero_factura }} - saldo {{ number_format($i->saldo_pendiente,2) }}</option>@endforeach</select></div>
<div class="col-md-2"><label>Fecha *</label><input type="date" name="fecha_pago" class="form-control" value="{{ now()->toDateString() }}"></div>
<div class="col-md-2"><label>Valor *</label><input type="number" step="0.01" min="0.01" name="valor" class="form-control" required></div>
<div class="col-md-3"><label>Método</label><input class="form-control" name="metodo_pago"></div>
<div class="col-md-3"><label>Origen fondos *</label><select class="form-select" name="origen_fondos" id="origen"><option value="caja_menor">Caja menor</option><option value="banco">Banco</option></select></div>
<div class="col-md-3" id="caja-wrap"><label>Caja</label><select name="caja_id" class="form-select"><option value="">Seleccione...</option>@foreach($cajas as $c)<option value="{{ $c->id }}">Caja #{{ $c->id }} saldo {{ number_format($c->valor,2) }}</option>@endforeach</select></div>
<div class="col-md-3 d-none" id="banco-wrap"><label>Banco</label><select name="banco_id" class="form-select"><option value="">Seleccione...</option>@foreach($bancos as $b)<option value="{{ $b->id }}">{{ $b->nombre }} saldo {{ number_format($b->saldo_actual,2) }}</option>@endforeach</select></div>
<div class="col-md-12"><label>Observaciones</label><textarea name="observaciones" class="form-control"></textarea></div>
</div><div class="mt-3"><button class="btn btn-primary">Registrar pago</button></div>
</form></div></div></div>
<script>document.getElementById('origen').addEventListener('change',e=>{document.getElementById('caja-wrap').classList.toggle('d-none',e.target.value!=='caja_menor');document.getElementById('banco-wrap').classList.toggle('d-none',e.target.value!=='banco');});</script>
@endsection
