@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-4">
    <h1 class="text-2xl font-bold">Editar plantilla</h1>
    <form method="POST" action="{{ route('consent-templates.update', $template) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('consents.templates.form')
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
        </div>
    </form>
</div>
@endsection
