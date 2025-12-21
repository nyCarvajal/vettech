@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Kardex</h1>
    <table class="table-auto w-full">
        <thead><tr><th>Fecha</th><th>Producto</th><th>Lote</th><th>Tipo</th><th>Cantidad</th><th>Razón</th></tr></thead>
        <tbody>
            @foreach($movements as $movement)
            <tr>
                <td>{{ $movement->created_at }}</td>
                <td>{{ $movement->product->name }}</td>
                <td>{{ $movement->batch?->batch_code }}</td>
                <td>{{ $movement->type }}</td>
                <td>{{ $movement->qty }}</td>
                <td>{{ $movement->reason }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $movements->links() }}
</div>
@endsection
