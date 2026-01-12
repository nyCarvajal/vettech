@extends('layouts.app')

@section('breadcrumbs')
    <a href="{{ route('groomings.index') }}" class="text-gray-400 hover:text-gray-600">Peluquería</a>
    <span class="mx-2">/</span>
    <a href="{{ route('groomings.show', $grooming) }}" class="text-gray-400 hover:text-gray-600">Orden #{{ $grooming->id }}</a>
    <span class="mx-2">/</span>
    <span class="font-medium text-gray-700">Informe</span>
@endsection

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 mb-1">Informe de baño</h1>
    <p class="text-sm text-gray-500 mb-4">Al guardar, el estado pasa automáticamente a finalizado.</p>

    <x-card title="Paciente">
        <p class="text-sm font-semibold text-gray-900">{{ $grooming->patient->display_name }}</p>
        <p class="text-sm text-gray-500">Tutor: {{ $grooming->owner->name }}</p>

        <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <div>
                <dt class="text-gray-500">Especie</dt>
                <dd class="font-medium text-gray-900">{{ optional($grooming->patient->species)->name ?? 'Sin registro' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Raza</dt>
                <dd class="font-medium text-gray-900">{{ optional($grooming->patient->breed)->name ?? 'Sin registro' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Identificación del tutor</dt>
                <dd class="font-medium text-gray-900">
                    {{ trim(($grooming->owner->document_type ? $grooming->owner->document_type . ' ' : '') . ($grooming->owner->document ?? '')) ?: 'Sin registro' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">WhatsApp</dt>
                <dd class="font-medium text-gray-900">{{ $grooming->owner->whatsapp ?: 'Sin registro' }}</dd>
            </div>
        </dl>
    </x-card>

    <form method="POST" action="{{ route('groomings.report.store', $grooming) }}" class="space-y-4">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="fleas" value="1" class="rounded text-mint-600" @checked(old('fleas'))>
                    ¿Presencia de pulgas?
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="ticks" value="1" class="rounded text-mint-600" @checked(old('ticks'))>
                    ¿Presencia de garrapatas?
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="skin_issue" value="1" class="rounded text-mint-600" @checked(old('skin_issue'))>
                    Problemas de piel
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="ear_issue" value="1" class="rounded text-mint-600" @checked(old('ear_issue'))>
                    Problemas de oído
                </label>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea name="observations" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:ring-mint-500 focus:border-mint-500">{{ old('observations') }}</textarea>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Recomendaciones</label>
                <textarea name="recommendations" rows="2" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus:ring-mint-500 focus:border-mint-500">{{ old('recommendations') }}</textarea>
            </div>
        </x-card>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center rounded-lg bg-purple-100 px-5 py-2 text-sm font-semibold text-purple-700 shadow-sm ring-1 ring-purple-200 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-300">Guardar informe</button>
        </div>
    </form>
@endsection
