@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Editar producto</h1>
    <form method="post" action="{{ route('products.update', $product) }}" class="space-y-2">
        @csrf
        @method('PUT')
        <input name="name" value="{{ $product->name }}" class="border p-2 w-full" required>
        <select name="type" class="border p-2 w-full">
            @foreach(['med'=>'Medicamento','insumo'=>'Insumo','alimento'=>'Alimento','servicio'=>'Servicio'] as $key=>$label)
                <option value="{{ $key }}" @selected($product->type==$key)>{{ $label }}</option>
            @endforeach
        </select>
        <input name="unit" value="{{ $product->unit }}" class="border p-2 w-full" required>
        <label class="block"><input type="checkbox" name="requires_batch" value="1" @checked($product->requires_batch)> Requiere lote</label>
        <input type="number" name="min_stock" value="{{ $product->min_stock }}" class="border p-2 w-full" required>
        <input type="number" step="0.01" name="sale_price" value="{{ $product->sale_price }}" class="border p-2 w-full" required>
        <button class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
