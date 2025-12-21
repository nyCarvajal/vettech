@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Nueva fórmula</h1>
    <form method="post" action="{{ route('prescriptions.store') }}" class="space-y-2">
        @csrf
        <input name="patient_id" placeholder="Paciente ID" class="border p-2 w-full" required>
        <input name="professional_id" placeholder="Profesional ID" class="border p-2 w-full" required>
        <input type="hidden" name="status" value="signed">
        <div class="border p-2">
            <h3 class="font-semibold">Ítem</h3>
            <input name="items[0][product_id]" placeholder="Producto ID" class="border p-2 w-full" required>
            <input name="items[0][dose]" placeholder="Dosis" class="border p-2 w-full" required>
            <input name="items[0][frequency]" placeholder="Frecuencia" class="border p-2 w-full" required>
            <input name="items[0][duration_days]" type="number" placeholder="Días" class="border p-2 w-full" required>
            <input name="items[0][qty_requested]" type="number" placeholder="Cantidad" class="border p-2 w-full" required>
        </div>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
