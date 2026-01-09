@extends('layouts.app')

@section('content')
@php
    $typeValue = old('type', 'product');
    $inventariableValue = old('inventariable', true) ? 'true' : 'false';
    $trackValue = old('track_inventory', true) ? 'true' : 'false';
@endphp
<div class="mx-auto w-full max-w-5xl px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Crear ítem</h1>
            <p class="text-sm text-slate-500">Registra un nuevo producto o servicio con precios e inventario.</p>
        </div>
        <a href="{{ route('items.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Volver</a>
    </div>

    <form method="POST" action="{{ route('items.store') }}" class="space-y-6" x-data="{ type: '{{ $typeValue }}', inventariable: {{ $inventariableValue }}, trackInventory: {{ $trackValue }} }" x-effect="if (type === 'service') { inventariable = false; trackInventory = false; }">
        @csrf

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Información básica</h2>
            <p class="text-sm text-slate-500">Completa los datos generales del ítem.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">SKU (código)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    @error('sku')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Categoría</label>
                    <select name="tipo" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categoryOptions as $option)
                            <option value="{{ $option }}" @selected((string) old('tipo') === (string) $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Área</label>
                    <select name="area" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                        <option value="">Selecciona un área</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" @selected((string) old('area') === (string) $area->id)>{{ $area->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Tipo</label>
                    <select name="type" x-model="type" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                        <option value="product">Producto</option>
                        <option value="service">Servicio</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Si eliges servicio, el ítem no será inventariable.</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Precios</h2>
            <p class="text-sm text-slate-500">Define los precios de venta y costo.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Precio de venta</label>
                    <input type="number" name="sale_price" step="0.01" min="0" value="{{ old('sale_price') }}" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    @error('sale_price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Costo</label>
                    <input type="number" name="cost_price" step="0.01" min="0" value="{{ old('cost_price') }}" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    @error('cost_price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Inventario</h2>
            <p class="text-sm text-slate-500">Controla el stock y los mínimos de alerta.</p>
            <div class="mt-4 space-y-4">
                <div class="flex items-center gap-4">
                    <input type="hidden" name="inventariable" value="0">
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="inventariable" value="1" x-model="inventariable" :disabled="type === 'service'" class="rounded border-slate-300 text-mint-600">
                        Inventariable
                    </label>
                    <input type="hidden" name="track_inventory" value="0">
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="track_inventory" value="1" x-model="trackInventory" :disabled="type === 'service'" class="rounded border-slate-300 text-mint-600">
                        Controlar stock
                    </label>
                </div>

                <div x-show="inventariable || trackInventory" x-cloak class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Stock inicial</label>
                        <input type="number" name="stock" step="0.001" min="0" value="{{ old('stock', 0) }}" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                        @error('stock')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Stock mínimo (alerta)</label>
                        <input type="number" name="cantidad" step="0.001" min="0" value="{{ old('cantidad', 0) }}" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                        @error('cantidad')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('items.index') }}" class="rounded-lg border border-violet-200 bg-violet-50 px-4 py-2 text-sm font-semibold text-violet-700 hover:bg-violet-100">Cancelar</a>
            <button type="submit" class="rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white hover:bg-mint-700">Guardar ítem</button>
        </div>
    </form>
</div>
@endsection
