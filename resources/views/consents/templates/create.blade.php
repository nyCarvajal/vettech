@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 space-y-6">
    <div class="bg-gradient-to-r from-emerald-50 via-white to-indigo-50 border border-slate-200 rounded-2xl p-6 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500">Consentimientos informados</p>
            <h1 class="text-2xl font-bold text-slate-800">Crear plantilla</h1>
            <p class="text-sm text-slate-500">Define el texto base y las etiquetas rápidas que podrás reutilizar.</p>
        </div>
        <div class="hidden sm:flex items-center space-x-2 text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-semibold">+</span>
            <div class="text-sm">
                <p class="font-semibold">Nuevo template</p>
                <p class="text-emerald-600">Editor a la izquierda · Etiquetas a la derecha</p>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('consent-templates.store') }}" class="space-y-6">
        @csrf
        @include('consents.templates.form')
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-sm">Guardar</button>
        </div>
    </form>
</div>
@endsection
