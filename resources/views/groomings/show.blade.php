@extends('layouts.app')

@section('breadcrumbs')
    <a href="{{ route('groomings.index') }}" class="text-gray-400 hover:text-gray-600">Peluquería</a>
    <span class="mx-2">/</span>
    <span class="font-medium text-gray-700">Detalle</span>
@endsection

@section('content')
    <div class="relative overflow-hidden rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50 via-white to-mint-50 shadow-lg">
        <div class="absolute inset-0 opacity-60" style="background-image: radial-gradient(circle at 10% 20%, rgba(167, 139, 250, 0.25) 0, transparent 30%), radial-gradient(circle at 80% 0%, rgba(16, 185, 129, 0.15) 0, transparent 25%);"></div>

        <div class="relative p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700 shadow-sm">
                        <span class="text-lg">✂️</span>
                        <span>Peluquería Premium</span>
                    </div>
                    <h1 class="mt-3 text-3xl font-bold text-purple-900">Orden #{{ $grooming->id }}</h1>
                    <p class="text-sm text-purple-700">{{ $grooming->scheduled_at?->format('d M Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $grooming->status_badge_class }}">
                        {{ strtoupper($grooming->status) }}
                    </span>
                    @if($grooming->status === 'agendado')
                        <form method="POST" action="{{ route('groomings.start', $grooming) }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-xl bg-mint-100 px-4 py-2 text-sm font-semibold text-mint-700 ring-1 ring-mint-200 shadow-sm transition hover:bg-mint-200">
                                <span>Iniciar</span>
                            </button>
                        </form>
                    @endif
                    @if(in_array($grooming->status, ['agendado','en_proceso']))
                        <form method="POST" action="{{ route('groomings.cancel', $grooming) }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 ring-1 ring-rose-200 transition hover:bg-rose-100">Cancelar</button>
                        </form>
                    @endif
                    @if($grooming->status === 'en_proceso')
                        <a href="{{ route('groomings.report.create', $grooming) }}" class="inline-flex items-center gap-2 rounded-xl bg-purple-50 px-4 py-2 text-sm font-semibold text-purple-700 ring-1 ring-purple-200 transition hover:bg-purple-100">Informe</a>
                    @endif
                    @if($grooming->product_service_id && $grooming->status === 'finalizado')
                        <form method="POST" action="{{ route('groomings.charge', $grooming) }}">
                            @csrf
                            <button class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-500 to-mint-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:scale-[1.02] hover:shadow-lg">Cobrar</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-card title="Paciente" class="border-purple-100 bg-white/70 shadow-sm">
                    <p class="text-base font-semibold text-purple-900">{{ $grooming->patient->display_name }}</p>
                    <p class="text-sm text-purple-600">Tutor: {{ $grooming->owner->name }}</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full bg-mint-50 px-3 py-1 font-semibold text-mint-700 ring-1 ring-mint-200">Cita #{{ $grooming->id }}</span>
                        @if($grooming->scheduled_at)
                            <span class="rounded-full bg-purple-50 px-3 py-1 font-semibold text-purple-700 ring-1 ring-purple-200">{{ $grooming->scheduled_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </x-card>

                <x-card title="Indicaciones" class="border-purple-100 bg-white/70 shadow-sm">
                    <p class="text-sm text-purple-800">{{ $grooming->indications ?: 'Sin notas' }}</p>
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        @if($grooming->needs_pickup)
                            <span class="rounded-full bg-mint-50 px-3 py-1 font-semibold text-mint-700 ring-1 ring-mint-200">Domicilio: {{ $grooming->pickup_address }}</span>
                        @endif
                        @if($grooming->external_deworming)
                            <span class="rounded-full bg-amber-50 px-3 py-1 font-semibold text-amber-700 ring-1 ring-amber-200">Desparasitación externa</span>
                            @if($grooming->deworming_source === 'inventory' && $grooming->dewormingProduct)
                                <span class="rounded-full bg-purple-50 px-3 py-1 font-semibold text-purple-700 ring-1 ring-purple-200">{{ $grooming->dewormingProduct->name }}</span>
                            @elseif($grooming->deworming_product_name)
                                <span class="rounded-full bg-purple-50 px-3 py-1 font-semibold text-purple-700 ring-1 ring-purple-200">{{ $grooming->deworming_product_name }}</span>
                            @endif
                        @endif
                    </div>
                </x-card>

                <x-card title="Servicio" class="border-purple-100 bg-white/70 shadow-sm">
                    @if($grooming->service_name)
                        <p class="text-base font-semibold text-purple-900">{{ $grooming->service_name }}</p>
                        @if($grooming->service_price)
                            <p class="text-sm text-purple-700">Precio: ${{ number_format($grooming->service_price, 0) }}</p>
                        @endif
                    @else
                        <p class="text-sm text-purple-700">Sin servicio asociado.</p>
                    @endif
                </x-card>
            </div>
        </div>
    </div>

    @if($grooming->report)
        <x-card title="Informe de baño" class="mt-4 border-purple-100 bg-white shadow-md">
            <div class="grid grid-cols-2 gap-2 text-sm md:grid-cols-4">
                <span class="flex items-center gap-2 rounded-lg bg-purple-50 px-3 py-2 text-purple-800">🐾 Pulgas: <strong>{{ $grooming->report->fleas ? 'Sí' : 'No' }}</strong></span>
                <span class="flex items-center gap-2 rounded-lg bg-purple-50 px-3 py-2 text-purple-800">🕷 Garrapatas: <strong>{{ $grooming->report->ticks ? 'Sí' : 'No' }}</strong></span>
                <span class="flex items-center gap-2 rounded-lg bg-mint-50 px-3 py-2 text-mint-800">🩹 Piel: <strong>{{ $grooming->report->skin_issue ? 'Sí' : 'No' }}</strong></span>
                <span class="flex items-center gap-2 rounded-lg bg-mint-50 px-3 py-2 text-mint-800">👂 Oído: <strong>{{ $grooming->report->ear_issue ? 'Sí' : 'No' }}</strong></span>
            </div>
            <div class="mt-4 rounded-xl bg-gradient-to-r from-purple-50 to-mint-50 p-4 text-sm text-purple-900">
                <p class="font-semibold">Observaciones</p>
                <p class="text-purple-800">{{ $grooming->report->observations ?: 'Sin observaciones' }}</p>
            </div>
            @if($grooming->report->recommendations)
                <div class="mt-3 rounded-xl bg-gradient-to-r from-mint-50 to-purple-50 p-4 text-sm text-purple-900">
                    <p class="font-semibold">Recomendaciones</p>
                    <p class="text-purple-800">{{ $grooming->report->recommendations }}</p>
                </div>
            @endif
        </x-card>
    @endif
@endsection
