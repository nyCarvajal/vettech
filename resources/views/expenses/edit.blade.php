@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Editar gasto</h1>
        <p class="text-sm text-gray-500">Actualiza la información del gasto.</p>
    </div>

    <form method="POST" action="{{ route('expenses.update', $expense) }}" class="bg-white border border-gray-200 rounded-xl p-6 shadow-soft space-y-4">
        @method('PUT')
        @include('expenses._form')
        <div class="flex gap-3">
            <button type="submit" class="bg-mint-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('expenses.index') }}" class="border border-gray-300 px-4 py-2 rounded">Cancelar</a>
        </div>
    </form>
</div>
@endsection
