@extends('layouts.app', ['subtitle' => 'Facturas de compra'])
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3"><h3>Facturas de compra</h3><a href="{{ route('supplier-invoices.create') }}" class="btn btn-primary">Nueva factura</a></div>
    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3"><select name="supplier_id" class="form-select"><option value="">Proveedor</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(request('supplier_id')==$s->id)>{{ $s->razon_social }}</option>@endforeach</select></div>
            <div class="col-md-2"><input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}"></div>
            <div class="col-md-2"><input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}"></div>
            <div class="col-md-2"><select name="estado_pago" class="form-select"><option value="">Estado pago</option>@foreach(['pendiente','parcial','pagado','vencido'] as $ep)<option value="{{ $ep }}" @selected(request('estado_pago')===$ep)>{{ ucfirst($ep) }}</option>@endforeach</select></div>
            <div class="col-md-1"><input type="checkbox" class="form-check-input mt-2" name="vencidas" value="1" @checked(request('vencidas'))> Vencidas</div>
            <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filtrar</button></div>
        </div>
    </form>
    <div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>Proveedor</th><th>Fecha</th><th>Total</th><th>Saldo</th><th>Estado</th><th></th></tr></thead><tbody>
        @foreach($invoices as $invoice)
            <tr><td>{{ $invoice->numero_factura }}</td><td>{{ $invoice->supplier->razon_social }}</td><td>{{ $invoice->fecha_factura?->format('Y-m-d') }}</td><td>{{ number_format($invoice->total,2) }}</td><td>{{ number_format($invoice->saldo_pendiente,2) }}</td><td>{{ $invoice->estado }}/{{ $invoice->estado_pago }}</td><td><a href="{{ route('supplier-invoices.show',$invoice) }}" class="btn btn-sm btn-outline-primary">Ver</a></td></tr>
        @endforeach
    </tbody></table></div></div>
    <div class="mt-2">{{ $invoices->links() }}</div>
</div>
@endsection
