@extends('layouts.app')

@section('content')
@php
    $selectedId = $selectedItem?->id;
    $statusOptions = [
        'disponible' => 'Disponible',
        'agotandose' => 'Agotándose',
        'agotado' => 'Agotado',
        'no_inventariable' => 'No inventariable',
    ];
@endphp
<div class="mx-auto w-full max-w-7xl px-4 py-6">
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    <div class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" class="grid gap-4 lg:grid-cols-4">
            <div>
                <label class="text-sm font-semibold text-slate-700">Filtrar por categoría</label>
                <select name="tipo" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    <option value="">Todas las categorías</option>
                    @foreach ($categoryOptions as $option)
                        <option value="{{ $option }}" @selected((string) request('tipo') === (string) $option)>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Filtro por estado</label>
                <select name="status" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    <option value="">Todos los estados</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Área</label>
                <select name="area" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" @selected((string) request('area') === (string) $area->id)>
                            {{ $area->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col justify-end">
                <label class="text-sm font-semibold text-slate-700">Buscar</label>
                <div class="relative mt-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3m1.8-4.2a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar producto o SKU"
                        class="w-full rounded-lg border-slate-200 py-2 pl-10 pr-3 text-sm">
                </div>
            </div>
            <div class="lg:col-span-4 flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                    Aplicar filtros
                </button>
                <a href="{{ route('items.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white shadow-sm">
                <span class="text-lg">+</span> Añadir producto
            </a>
            <button type="button" class="hidden h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 lg:flex">
                <i class="ri-layout-grid-line"></i>
            </button>
            <button type="button" class="hidden h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-500 lg:flex">
                <i class="ri-menu-line"></i>
            </button>
        </div>
        <div class="text-sm text-slate-500">{{ $items->total() }} productos</div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3">Lote</th>
                                <th class="px-4 py-3">Caducidad</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($items as $item)
                                @php
                                    $rowUrl = route('items.index', array_merge(request()->query(), ['selected' => $item->id]));
                                    $isSelected = $selectedId === $item->id;
                                @endphp
                                <tr class="cursor-pointer transition hover:bg-slate-50 {{ $isSelected ? 'bg-mint-50/60' : '' }}" onclick="window.location='{{ $rowUrl }}'">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M7 7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m7 7 1-3h8l1 3" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-800">{{ $item->nombre }}</p>
                                                <p class="text-xs text-slate-500">{{ $item->areaRelation?->descripcion ?? $item->tipo ?? 'Sin categoría' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $item->sku ?? '—' }}</td>
                                    <td class="px-4 py-4 text-slate-400">—</td>
                                    <td class="px-4 py-4 font-semibold text-slate-700">{{ number_format((float) $item->stock, 3, ',', '.') }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->status_color }}">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                                        No hay productos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $items->links() }}</div>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky top-6">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    @if ($selectedItem)
                        <div class="flex flex-col items-center text-center">
                            <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V5a4 4 0 0 1 8 0v2" />
                                </svg>
                            </div>
                            <h2 class="mt-4 text-lg font-semibold text-slate-800">{{ $selectedItem->nombre }}</h2>
                            <p class="text-sm text-slate-500">SKU: {{ $selectedItem->sku ?? 'Sin SKU' }}</p>
                            <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $selectedItem->status_color }}">
                                {{ $selectedItem->status_label }}
                            </span>
                        </div>

                        <div class="mt-6" x-data="{
                            open: false,
                            type: 'entry',
                            actions: {
                                entry: '{{ route('items.movements.entry', $selectedItem) }}',
                                exit: '{{ route('items.movements.exit', $selectedItem) }}',
                                adjust: '{{ route('items.movements.adjust', $selectedItem) }}',
                            }
                        }">
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="type='entry'; open=true" class="rounded-lg border border-mint-200 bg-mint-50 px-3 py-2 text-xs font-semibold text-mint-700 hover:border-mint-600 hover:bg-mint-100">Entrada</button>
                                <button type="button" @click="type='exit'; open=true" class="rounded-lg border border-violet-200 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700 hover:border-violet-400 hover:bg-violet-100">Salida</button>
                                <button type="button" @click="type='adjust'; open=true" class="rounded-lg border border-mint-200 bg-mint-50 px-3 py-2 text-xs font-semibold text-mint-700 hover:border-mint-600 hover:bg-mint-100">Ajuste</button>
                            </div>

                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                                <div @click.away="open=false" class="w-full max-w-md rounded-xl bg-white p-6 shadow-lg">
                                    <h3 class="text-lg font-semibold text-slate-800">Registrar <span x-text="type === 'entry' ? 'entrada' : type === 'exit' ? 'salida' : 'ajuste'"></span></h3>
                                    <form method="POST" x-bind:action="actions[type]" class="mt-4 space-y-4">
                                        @csrf
                                        <div>
                                            <label class="text-sm font-semibold text-slate-700">Cantidad</label>
                                            <input type="number" name="quantity" step="0.001" min="0" required class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="text-sm font-semibold text-slate-700">Referencia</label>
                                            <input type="text" name="reference" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Factura, lote, orden">
                                        </div>
                                        <div>
                                            <label class="text-sm font-semibold text-slate-700">Notas</label>
                                            <textarea name="notes" rows="2" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm" placeholder="Notas internas"></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="open=false" class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Cancelar</button>
                                            <button type="submit" class="rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-slate-700">Historial de movimientos</h3>
                                <a href="{{ route('items.movements.index', $selectedItem) }}" class="text-xs font-semibold text-mint-700">Ver todo</a>
                            </div>
                            <div class="space-y-3">
                                @forelse ($selectedItem->inventoryMovements as $movement)
                                    <div class="flex items-start justify-between rounded-lg border border-slate-100 px-3 py-2">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-700">
                                                {{ $movement->occurred_at?->format('d/m/Y H:i') ?? $movement->created_at?->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ match ($movement->movement_type) {
                                                    'entry' => 'Entrada',
                                                    'exit' => 'Salida',
                                                    'adjust' => 'Ajuste',
                                                    'initial' => 'Inicial',
                                                    'sale' => 'Venta',
                                                    'sale_void' => 'Anulación',
                                                    default => 'Movimiento',
                                                } }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-semibold text-slate-700">{{ number_format((float) $movement->quantity, 3, ',', '.') }}</p>
                                            <p class="text-[11px] text-slate-400">Stock: {{ number_format((float) $movement->after_stock, 3, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">Sin movimientos recientes.</p>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center text-sm text-slate-500">
                            Selecciona un producto para ver el detalle.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
