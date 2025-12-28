@extends('layouts.app')

@section('breadcrumbs')
    <a href="{{ route('groomings.index') }}" class="text-gray-400 hover:text-gray-600">Peluquería</a>
    <span class="mx-2">/</span>
    <span class="font-medium text-gray-700">Nuevo ingreso</span>
@endsection

@section('content')
    <div class="rounded-2xl bg-gradient-to-r from-purple-50 via-white to-mint-50 border border-purple-100 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Registrar peluquería</h1>
                <p class="text-sm text-gray-600">Agenda y captura indicaciones en un solo paso.</p>
            </div>
            <span class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Flujo AGENDADO</span>
        </div>
    </div>

    <form method="POST" action="{{ route('groomings.store') }}" class="mt-6 space-y-6">
        @csrf
        <x-card class="border-purple-100 shadow-soft">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Paciente</label>
                    <select name="patient_id" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400" required>
                        <option value="">Selecciona un paciente</option>
                        @foreach($patients as $pet)
                            <option value="{{ $pet->id }}" @selected(old('patient_id', optional($patient)->id) == $pet->id)>
                                {{ $pet->display_name }} ({{ optional($pet->owner)->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tutor</label>
                    <select name="owner_id" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400" required>
                        <option value="">Selecciona un tutor</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" @selected(old('owner_id', optional($patient?->owner)->id) == $owner->id)>
                                {{ $owner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hora del servicio</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') ?? now()->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Indicaciones</label>
                    <textarea name="indications" rows="3" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400" placeholder="Notas del tutor o del staff">{{ old('indications') }}</textarea>
                </div>
            </div>
        </x-card>

        <x-card title="Domicilio y desparasitación" class="border-purple-100 shadow-soft">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="needs_pickup" value="1" class="rounded text-purple-600" @checked(old('needs_pickup'))>
                        ¿Requiere domicilio?
                    </label>
                    <input type="text" name="pickup_address" value="{{ old('pickup_address') }}" placeholder="Dirección de recogida" class="mt-2 w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="external_deworming" value="1" class="rounded text-purple-600" @checked(old('external_deworming'))>
                        ¿Desparasitación externa?
                    </label>
                    <div class="mt-2 space-y-2">
                        <select name="deworming_source" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                            <option value="none" @selected(old('deworming_source','none')==='none')>No aplica</option>
                            <option value="inventory" @selected(old('deworming_source')==='inventory')>Desde inventario</option>
                            <option value="manual" @selected(old('deworming_source')==='manual')>Ingreso manual</option>
                        </select>
                        <select name="deworming_product_id" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                            <option value="">Producto de inventario</option>
                            @foreach($inventoryProducts as $product)
                                <option value="{{ $product->id }}" @selected(old('deworming_product_id')==$product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="deworming_product_name" value="{{ old('deworming_product_name') }}" placeholder="Nombre del producto manual" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400">
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Servicio opcional" class="border-purple-100 shadow-soft bg-purple-50/50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Selecciona un servicio de inventario (no inventariable). El precio se toma automáticamente.</p>
                    <select name="product_service_id" class="w-full rounded-lg border-purple-200 focus:ring-purple-400 focus:border-purple-400 bg-white">
                        <option value="">Sin servicio por ahora</option>
                        @foreach($serviceProducts as $product)
                            <option value="{{ $product->id }}" @selected(old('product_service_id')==$product->id)>{{ $product->name }} ({{ number_format($product->sale_price,0) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <div class="bg-gradient-to-r from-purple-100 to-mint-100 text-purple-800 border border-purple-200 rounded-xl p-4 w-full">
                        <p class="text-sm font-semibold">Recuerda</p>
                        <p class="text-sm text-gray-700">El cobro se puede hacer después. Solo elegimos el servicio para dejarlo listo.</p>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-500 to-mint-500 text-white shadow-lg hover:from-purple-400 hover:to-mint-400 transition">
                Guardar y agendar
            </button>
        </div>
    </form>
@endsection
