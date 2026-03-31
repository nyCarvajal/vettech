@extends('layouts.app', ['subtitle' => 'Detalle pago proveedor'])
@section('content')
<div class="container-fluid"><div class="card"><div class="card-body">
<h4>Pago #{{ $payment->id }}</h4>
<p>Proveedor: {{ $payment->supplier->razon_social }}</p>
<p>Factura: {{ $payment->invoice->numero_factura ?? 'N/A' }}</p>
<p>Valor: {{ number_format($payment->valor,2) }}</p>
<p>Origen: {{ $payment->origen_fondos }} {{ $payment->banco?->nombre ?? $payment->caja?->id }}</p>
</div></div></div>
@endsection
