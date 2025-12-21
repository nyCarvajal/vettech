@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Lotes</h1>
    <a href="{{ route('batches.create') }}" class="btn btn-primary">Agregar lote</a>
    <table class="table-auto w-full mt-4">
        <thead><tr><th>Producto</th><th>Código</th><th>Vence</th><th>Disponible</th></tr></thead>
        <tbody>
            @foreach($batches as $batch)
            <tr>
                <td>{{ $batch->product->name }}</td>
                <td>{{ $batch->batch_code }}</td>
                <td>{{ $batch->expires_at->format('Y-m-d') }}</td>
                <td>{{ $batch->qty_available }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $batches->links() }}
</div>
@endsection
