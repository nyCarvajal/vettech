@extends('layouts.app', ['subtitle' => 'Pagos a proveedores'])
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between mb-3"><h3>Pagos a proveedores</h3><a href="{{ route('supplier-payments.create') }}" class="btn btn-primary">Registrar pago</a></div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Fecha</th><th>Proveedor</th><th>Factura</th><th>Valor</th><th>Origen</th><th></th></tr></thead><tbody>@foreach($payments as $p)<tr><td>{{ $p->fecha_pago?->format('Y-m-d') }}</td><td>{{ $p->supplier->razon_social }}</td><td>{{ $p->invoice->numero_factura ?? 'Anticipo' }}</td><td>{{ number_format($p->valor,2) }}</td><td>{{ $p->origen_fondos }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('supplier-payments.show',$p) }}">Ver</a></td></tr>@endforeach</tbody></table></div></div>
<div class="mt-2">{{ $payments->links() }}</div></div>
@endsection
