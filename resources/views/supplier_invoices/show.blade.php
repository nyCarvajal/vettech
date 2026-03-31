@extends('layouts.app', ['subtitle' => 'Detalle factura'])
@section('content')
<div class="container-fluid"><div class="card"><div class="card-body">
    <div class="d-flex justify-content-between"><h4>Factura {{ $invoice->numero_factura }}</h4>
    @if($invoice->estado !== 'anulada')
        <form method="POST" action="{{ route('supplier-invoices.cancel', $invoice) }}">@csrf <button class="btn btn-outline-danger" onclick="return confirm('¿Anular factura?')">Anular factura</button></form>
    @endif
    </div>
    <p>Proveedor: {{ $invoice->supplier->razon_social }} | Estado: {{ $invoice->estado }} | Pago: {{ $invoice->estado_pago }}</p>
    <table class="table"><thead><tr><th>Producto</th><th>Cantidad</th><th>Costo</th><th>Obsequio</th><th>Subtotal</th></tr></thead><tbody>@foreach($invoice->details as $d)<tr><td>{{ $d->item->nombre ?? $d->descripcion }}</td><td>{{ $d->cantidad }}</td><td>{{ number_format($d->costo_unitario,2) }}</td><td>{!! $d->es_obsequio ? '<span class="badge bg-warning">Sí</span>' : 'No' !!}</td><td>{{ number_format($d->subtotal,2) }}</td></tr>@endforeach</tbody></table>
    <h5 class="text-end">Total {{ number_format($invoice->total,2) }} | Pagado {{ number_format($invoice->total_pagado,2) }} | Saldo {{ number_format($invoice->saldo_pendiente,2) }}</h5>
</div></div></div>
@endsection
