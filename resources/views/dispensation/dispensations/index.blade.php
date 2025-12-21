@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Dispensar</h1>
    @foreach($pending as $prescription)
        <div class="border p-2 mb-2">
            <div class="font-semibold">Paciente #{{ $prescription->patient_id }}</div>
            <form method="post" action="{{ route('dispensations.store', $prescription) }}" class="space-y-2 mt-2">
                @csrf
                @foreach($prescription->items as $i => $item)
                    <div class="border p-2">
                        <div>{{ $item->product->name ?? 'Producto '.$item->product_id }}</div>
                        <label>Cantidad a dispensar</label>
                        <input type="number" name="items[{{ $i }}][qty]" value="{{ $item->qty_requested }}" class="border p-2 w-full">
                        <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                    </div>
                @endforeach
                <button class="btn btn-primary">Dispensar</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
