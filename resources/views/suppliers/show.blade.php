@extends('layouts.app', ['subtitle' => 'Detalle proveedor'])
@section('content')
<div class="container-fluid"><div class="card"><div class="card-body">
<h4>{{ $supplier->razon_social }}</h4><p class="text-muted">{{ $supplier->tipo_documento }} {{ $supplier->numero_documento }}</p>
<div class="row"><div class="col-md-6"><strong>Contacto:</strong> {{ $supplier->contacto_principal }}</div><div class="col-md-6"><strong>Email:</strong> {{ $supplier->email }}</div></div>
<hr><h6>Resumen</h6><p>Facturas: {{ $supplier->invoices->count() }} | Pagos: {{ $supplier->payments->count() }}</p>
</div></div></div>
@endsection
