@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Nuevo producto</h1>
    <form method="post" action="{{ route('products.store') }}" class="space-y-2">
        @csrf
        <input name="name" placeholder="Nombre" class="border p-2 w-full" required>
        <select name="type" class="border p-2 w-full">
            <option value="med">Medicamento</option>
            <option value="insumo">Insumo</option>
            <option value="alimento">Alimento</option>
            <option value="servicio">Servicio</option>
        </select>
        <input name="unit" placeholder="Unidad" class="border p-2 w-full" required>
        <label class="block"><input type="checkbox" name="requires_batch" value="1"> Requiere lote</label>
        <input type="number" name="min_stock" placeholder="Stock mínimo" class="border p-2 w-full" required>
        <input type="number" step="0.01" name="sale_price" placeholder="Precio venta" class="border p-2 w-full" required>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
