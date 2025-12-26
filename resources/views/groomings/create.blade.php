@extends('layouts.app')

@section('breadcrumbs')
    <a href="{{ route('groomings.index') }}" class="text-gray-400 hover:text-gray-600">Peluquería</a>
    <span class="mx-2">/</span>
    <span class="font-medium text-gray-700">Nuevo ingreso</span>
@endsection

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Registrar peluquería</h1>
            <p class="text-sm text-gray-500">Agenda y captura indicaciones en un solo paso.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('groomings.store') }}" class="mt-6 space-y-6">
        @csrf
        <x-card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paciente</label>
                    <select name="patient_id" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500" required>
                        <option value="">Selecciona un paciente</option>
                        @foreach($patients as $pet)
                            <option value="{{ $pet->id }}" @selected(old('patient_id', optional($patient)->id) == $pet->id)>
                                {{ $pet->display_name }} ({{ optional($pet->owner)->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tutor</label>
                    <select name="owner_id" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500" required>
                        <option value="">Selecciona un tutor</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" @selected(old('owner_id', optional($patient?->owner)->id) == $owner->id)>
                                {{ $owner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora del servicio</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') ?? now()->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Indicaciones</label>
                    <textarea name="indications" rows="3" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500" placeholder="Notas del tutor o del staff">{{ old('indications') }}</textarea>
                </div>
            </div>
        </x-card>

        <x-card title="Domicilio y desparasitación">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="needs_pickup" value="1" class="rounded text-mint-600" @checked(old('needs_pickup'))>
                        ¿Requiere domicilio?
                    </label>
                    <input type="text" name="pickup_address" value="{{ old('pickup_address') }}" placeholder="Dirección de recogida" class="mt-2 w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="external_deworming" value="1" class="rounded text-mint-600" @checked(old('external_deworming'))>
                        ¿Desparasitación externa?
                    </label>
                    <div class="mt-2 space-y-2">
                        <select name="deworming_source" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                            <option value="none" @selected(old('deworming_source','none')==='none')>No aplica</option>
                            <option value="inventory" @selected(old('deworming_source')==='inventory')>Desde inventario</option>
                            <option value="manual" @selected(old('deworming_source')==='manual')>Ingreso manual</option>
                        </select>
                        <select name="deworming_product_id" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                            <option value="">Producto de inventario</option>
                            @foreach($inventoryProducts as $product)
                                <option value="{{ $product->id }}" @selected(old('deworming_product_id')==$product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="deworming_product_name" value="{{ old('deworming_product_name') }}" placeholder="Nombre del producto manual" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Servicio opcional">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fuente</label>
                    <select name="service_source" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                        <option value="none" @selected(old('service_source','none')==='none')>Sin cobro por ahora</option>
                        <option value="product" @selected(old('service_source')==='product')>Producto (servicio)</option>
                        <option value="grooming_service" @selected(old('service_source')==='grooming_service')>Catálogo de peluquería</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servicio / producto</label>
                    <select name="product_service_id" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                        <option value="">Servicio desde productos</option>
                        @foreach($serviceProducts as $product)
                            <option value="{{ $product->id }}" @selected(old('product_service_id')==$product->id)>{{ $product->name }} ({{ number_format($product->sale_price,0) }})</option>
                        @endforeach
                    </select>
                    <select name="service_id" class="mt-2 w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                        <option value="">Catálogo de peluquería</option>
                        @foreach($groomingServices as $service)
                            <option value="{{ $service->id }}" @selected(old('service_id')==$service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                    <input type="number" step="0.01" name="service_price" value="{{ old('service_price') }}" placeholder="Opcional" class="w-full rounded-lg border-gray-200 focus:ring-mint-500 focus:border-mint-500">
                </div>
            </div>
        </x-card>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 bg-mint-600 text-white px-5 py-2 rounded-lg shadow-sm hover:bg-mint-500">
                Guardar y agendar
            </button>
        </div>
    </form>
@endsection
