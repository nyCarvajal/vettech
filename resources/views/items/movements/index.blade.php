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
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Movimientos de {{ $item->nombre }}</h1>
            <p class="text-sm text-slate-500">Historial completo de entradas, salidas y ajustes.</p>
        </div>
        <a href="{{ route('items.show', $item) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Volver</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Cantidad</th>
                    <th class="px-4 py-3">Stock anterior</th>
                    <th class="px-4 py-3">Stock final</th>
                    <th class="px-4 py-3">Referencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($movements as $movement)
                    <tr>
                        <td class="px-4 py-3 text-slate-600">{{ $movement->occurred_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $statusOptions[$movement->movement_type] ?? 'Movimiento' }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ number_format((float) $movement->quantity, 3, ',', '.') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ number_format((float) $movement->before_stock, 3, ',', '.') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ number_format((float) $movement->after_stock, 3, ',', '.') }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $movement->reference ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No hay movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $movements->links() }}</div>
</div>
@endsection
