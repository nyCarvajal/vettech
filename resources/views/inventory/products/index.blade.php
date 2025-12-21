@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Productos</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Nuevo producto</a>
    <table class="table-auto w-full mt-4">
        <thead><tr><th>Nombre</th><th>Tipo</th><th>Unidad</th><th>Precio</th><th></th></tr></thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->type }}</td>
                <td>{{ $product->unit }}</td>
                <td>{{ $product->sale_price }}</td>
                <td><a href="{{ route('products.edit', $product) }}" class="text-blue-500">Editar</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $products->links() }}
</div>
@endsection
