@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Venta #{{ $sale->id }}</h1>
    <p>Estado: {{ $sale->status }} | Total: ${{ $sale->total }}</p>
    <table class="table-auto w-full mt-2">
        <thead><tr><th>Producto</th><th>Cant</th><th>Precio</th></tr></thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product_id }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->unit_price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
