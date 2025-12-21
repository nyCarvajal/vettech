@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="text-xl font-bold mb-4">Nueva tarea</h1>
    <form method="post" action="{{ route('hospital.tasks.store') }}" class="space-y-2">
        @csrf
        <input name="stay_id" placeholder="Stay ID" class="border p-2 w-full" required>
        <select name="category" class="border p-2 w-full">
            <option value="med">Medicamento</option>
            <option value="fluidos">Fluidos</option>
            <option value="alimento">Alimento</option>
            <option value="control">Control</option>
            <option value="procedimiento">Procedimiento</option>
        </select>
        <input name="title" placeholder="Título" class="border p-2 w-full" required>
        <textarea name="instructions" class="border p-2 w-full" placeholder="Instrucciones"></textarea>
        <input type="text" name="times_json[]" placeholder="07:00" class="border p-2 w-full">
        <input type="datetime-local" name="start_at" class="border p-2 w-full" required>
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
