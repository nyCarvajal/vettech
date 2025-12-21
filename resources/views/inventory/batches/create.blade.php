@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Nuevo lote</h1>
    <form method="post" action="{{ route('batches.store') }}" class="space-y-2">
        @csrf
        <select name="product_id" class="border p-2 w-full">
            @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
        <input name="batch_code" placeholder="Código" class="border p-2 w-full" required>
        <input type="date" name="expires_at" class="border p-2 w-full" required>
        <input type="number" step="0.01" name="cost" class="border p-2 w-full" placeholder="Costo" required>
        <input type="number" name="qty_in" class="border p-2 w-full" placeholder="Cantidad" required>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
