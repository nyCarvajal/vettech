@extends('layouts.app')

@section('breadcrumbs')
    <a href="{{ route('groomings.index') }}" class="text-gray-400 hover:text-gray-600">Peluquería</a>
    <span class="mx-2">/</span>
    <span class="font-medium text-gray-700">Detalle</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Orden #{{ $grooming->id }}</h1>
            <p class="text-sm text-gray-500">{{ $grooming->scheduled_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $grooming->status_badge_class }}">
                {{ strtoupper($grooming->status) }}
            </span>
            @if($grooming->status === 'agendado')
                <form method="POST" action="{{ route('groomings.start', $grooming) }}">
                    @csrf
                    <button class="inline-flex items-center bg-mint-600 text-white px-3 py-2 rounded-lg text-sm">Iniciar</button>
                </form>
            @endif
            @if(in_array($grooming->status, ['agendado','en_proceso']))
                <form method="POST" action="{{ route('groomings.cancel', $grooming) }}">
                    @csrf
                    <button class="inline-flex items-center bg-rose-100 text-rose-700 px-3 py-2 rounded-lg text-sm">Cancelar</button>
                </form>
            @endif
            @if($grooming->status === 'en_proceso')
                <a href="{{ route('groomings.report.create', $grooming) }}" class="inline-flex items-center bg-mint-50 text-mint-700 px-3 py-2 rounded-lg text-sm">Informe</a>
            @endif
            @if($grooming->product_service_id && $grooming->status === 'finalizado')
                <form method="POST" action="{{ route('groomings.charge', $grooming) }}">
                    @csrf
                    <button class="inline-flex items-center bg-emerald-600 text-white px-3 py-2 rounded-lg text-sm">Cobrar</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card title="Paciente">
            <p class="text-sm font-semibold text-gray-900">{{ $grooming->patient->display_name }}</p>
            <p class="text-sm text-gray-500">Tutor: {{ $grooming->owner->name }}</p>
        </x-card>

        <x-card title="Indicaciones">
            <p class="text-sm text-gray-700">{{ $grooming->indications ?: 'Sin notas' }}</p>
            <div class="flex flex-wrap gap-2 mt-3 text-xs">
                @if($grooming->needs_pickup)
                    <span class="px-2 py-1 bg-mint-50 text-mint-700 rounded-full">Domicilio: {{ $grooming->pickup_address }}</span>
                @endif
                @if($grooming->external_deworming)
                    <span class="px-2 py-1 bg-amber-50 text-amber-700 rounded-full">Desparasitación externa</span>
                    @if($grooming->deworming_source === 'inventory' && $grooming->dewormingProduct)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full">{{ $grooming->dewormingProduct->name }}</span>
                    @elseif($grooming->deworming_product_name)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full">{{ $grooming->deworming_product_name }}</span>
                    @endif
                @endif
            </div>
        </x-card>

        <x-card title="Servicio">
            @if($grooming->service_name)
                <p class="text-sm font-semibold text-gray-900">{{ $grooming->service_name }}</p>
                @if($grooming->service_price)
                    <p class="text-sm text-gray-600">Precio: ${{ number_format($grooming->service_price, 0) }}</p>
                @endif
            @else
                <p class="text-sm text-gray-500">Sin servicio asociado.</p>
            @endif
        </x-card>
    </div>

    @if($grooming->report)
        <x-card title="Informe de baño" class="mt-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                <span class="flex items-center gap-2">🐾 Pulgas: <strong>{{ $grooming->report->fleas ? 'Sí' : 'No' }}</strong></span>
                <span class="flex items-center gap-2">🕷 Garrapatas: <strong>{{ $grooming->report->ticks ? 'Sí' : 'No' }}</strong></span>
                <span class="flex items-center gap-2">🩹 Piel: <strong>{{ $grooming->report->skin_issue ? 'Sí' : 'No' }}</strong></span>
                <span class="flex items-center gap-2">👂 Oído: <strong>{{ $grooming->report->ear_issue ? 'Sí' : 'No' }}</strong></span>
            </div>
            <div class="mt-3 text-sm text-gray-700">
                <p class="font-semibold">Observaciones</p>
                <p>{{ $grooming->report->observations ?: 'Sin observaciones' }}</p>
            </div>
            @if($grooming->report->recommendations)
                <div class="mt-2 text-sm text-gray-700">
                    <p class="font-semibold">Recomendaciones</p>
                    <p>{{ $grooming->report->recommendations }}</p>
                </div>
            @endif
        </x-card>
    @endif
@endsection
