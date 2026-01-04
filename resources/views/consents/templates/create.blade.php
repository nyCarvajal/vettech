@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-4">
    <h1 class="text-2xl font-bold">Crear plantilla</h1>
    <form method="POST" action="{{ route('consent-templates.store') }}" class="space-y-4">
        @csrf
        @include('consents.templates.form')
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
        </div>
    </form>
</div>
@endsection
