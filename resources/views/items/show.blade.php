@extends('layouts.app')

@section('content')
@php
    $statusOptions = [
        'entry' => 'Entrada',
        'exit' => 'Salida',
        'adjust' => 'Ajuste',
        'initial' => 'Inicial',
        'sale' => 'Venta',
        'sale_void' => 'Anulación',
        'adjustment' => 'Ajuste',
    ];
@endphp
<div class="mx-auto w-full max-w-6xl px-4 py-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">{{ $item->nombre }}</h1>
            <p class="text-sm text-slate-500">SKU: {{ $item->sku ?? 'Sin SKU' }}</p>
        </div>
        <a href="{{ route('items.index', ['selected' => $item->id]) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Volver</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-28 w-28 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V5a4 4 0 0 1 8 0v2" />
                    </svg>
                </div>
                <span class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $item->status_color }}">
                    {{ $item->status_label }}
                </span>
            </div>
            <div class="mt-6 space-y-4 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Área</span>
                    <span class="font-semibold text-slate-800">{{ $item->areaRelation?->descripcion ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Categoría</span>
                    <span class="font-semibold text-slate-800">{{ $item->tipo ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Tipo</span>
                    <span class="font-semibold text-slate-800">{{ $item->type === 'service' ? 'Servicio' : 'Producto' }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Precio venta</span>
                    <span class="font-semibold text-slate-800">$ {{ number_format((float) $item->sale_price, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Costo</span>
                    <span class="font-semibold text-slate-800">$ {{ number_format((float) $item->cost_price, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase text-slate-400">Stock actual</p>
                    <p class="text-xl font-semibold text-slate-800">{{ number_format((float) $item->stock, 3, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase text-slate-400">Stock mínimo</p>
                    <p class="text-xl font-semibold text-slate-800">{{ number_format((float) $item->cantidad, 3, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase text-slate-400">Estado</p>
                    <p class="text-xl font-semibold text-slate-800">{{ $item->status_label }}</p>
                </div>
            </div>

            <div class="mt-6" x-data="{
                open: false,
                type: 'entry',
                actions: {
                    entry: '{{ route('items.movements.entry', $item) }}',
                    exit: '{{ route('items.movements.exit', $item) }}',
                    adjust: '{{ route('items.movements.adjust', $item) }}',
                }
            }">
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="type='entry'; open=true" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Entrada</button>
                    <button type="button" @click="type='exit'; open=true" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Salida</button>
                    <button type="button" @click="type='adjust'; open=true" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Ajuste</button>
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
                                <input type="text" name="reference" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Notas</label>
                                <textarea name="notes" rows="2" class="mt-2 w-full rounded-lg border-slate-200 px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="open=false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Cancelar</button>
                                <button type="submit" class="rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">Historial de movimientos</h2>
                    <a href="{{ route('items.movements.index', $item) }}" class="text-sm font-semibold text-mint-700">Ver todo</a>
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Cantidad</th>
                                <th class="px-4 py-3">Stock final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($movements as $movement)
                                <tr>
                                    <td class="px-4 py-3 text-slate-600">{{ $movement->occurred_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $statusOptions[$movement->movement_type] ?? 'Movimiento' }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ number_format((float) $movement->quantity, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ number_format((float) $movement->after_stock, 3, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No hay movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $movements->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
