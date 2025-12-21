@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Ventas</h1>
    <form method="post" action="{{ route('sales.store') }}" class="space-y-2 mb-4">
        @csrf
        <button class="btn btn-primary">Crear venta abierta</button>
    </form>
    <ul>
        @foreach($sales as $sale)
            <li><a class="text-blue-500" href="{{ route('sales.show', $sale) }}">Venta #{{ $sale->id }} - {{ $sale->status }} - ${{ $sale->total }}</a></li>
        @endforeach
    </ul>
    {{ $sales->links() }}
</div>
@endsection
