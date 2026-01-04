@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 space-y-4">
    <h1 class="text-2xl font-semibold text-gray-800">Editar procedimiento {{ $procedure->code }}</h1>
    <form method="POST" action="{{ route('procedures.update', $procedure) }}">
        @method('PUT')
        @include('procedures._form')
        <div class="mt-4 flex justify-end space-x-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
        </div>
    </form>
</div>
@endsection
