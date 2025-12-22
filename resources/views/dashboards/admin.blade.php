@extends('layouts.app')

@section('content')
    @include('partials.page-header', [
        'title' => 'Dashboard administrador',
        'subtitle' => 'Visión rápida del negocio y operación',
        'actions' => view('components.inline-actions', [
            'actions' => [
                ['label' => 'Crear usuario / rol', 'route' => route('users.admins.create')],
                ['label' => 'Crear producto / lote', 'route' => route('items.create')],
                ['label' => 'Ver reportes', 'route' => route('kardex.index')],
            ],
        ]),
    ])

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
        <x-kpi label="Ventas HOY" :value="'$' . number_format($metrics['todaySales']->sum('total'), 0, ',', '.')"
               :hint="$metrics['todaySales']->map(fn ($sale) => ucfirst($sale->method) . ': $' . number_format($sale->total, 0, ',', '.'))->implode(' | ')" />

        @php
            $monthTotal = $metrics['monthSales']->sum('total');
            $growth = $metrics['previousMonthSales'] > 0
                ? round((($monthTotal - $metrics['previousMonthSales']) / max($metrics['previousMonthSales'], 1)) * 100)
                : 100;
        @endphp
        <x-kpi label="Ventas MES" :value="'$' . number_format($monthTotal, 0, ',', '.')" :hint="'Vs mes anterior: ' . $growth . '%'" />

        <x-kpi label="Sesiones de caja"
               :value="$metrics['cashSessions']->whereNull('closed_at')->count() . ' abiertas / ' . $metrics['cashSessions']->count()"
               :hint="'Alertas: ' . $metrics['cashSessions']->whereNull('closed_at')->count() . ' sin cierre'" />

        <x-kpi label="Ocupación hospital"
               :value="($metrics['hospitalOccupancy']->active ?? 0) . ' / ' . ($metrics['hospitalOccupancy']->total ?? 0)"
               :hint="'Ingresos HOY: $' . number_format($metrics['hospitalRevenueToday'], 0, ',', '.') . ' | Mes: $' . number_format($metrics['hospitalRevenueMonth'], 0, ',', '.')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <x-card title="Inventario bajo stock (top 10)" subtitle="Prioridad">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['lowStock'] as $product)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">Stock: {{ $product->stock_available ?? 0 }} / Min: {{ $product->min_stock ?? 0 }}</p>
                        </div>
                        <x-badge variant="warning" :text="'SKU ' . ($product->sku ?? '-')" />
                    </div>
                @empty
                    <x-empty title="Sin alertas de stock" description="Todos los productos están dentro del mínimo"></x-empty>
                @endforelse
            </div>
        </x-card>

        <x-card title="Lotes por vencer (30 días)" subtitle="Top 10">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['expiringBatches'] as $batch)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $batch->product->name ?? 'Producto' }}</p>
                            <p class="text-sm text-gray-500">Lote {{ $batch->batch_code ?? '-' }} · vence {{ optional($batch->expires_at)->format('d/m') }}</p>
                        </div>
                        <x-badge variant="mint" :text="$batch->qty_available . ' uds'" />
                    </div>
                @empty
                    <x-empty title="Sin lotes próximos a vencer" description="No hay vencimientos en los próximos 30 días"></x-empty>
                @endforelse
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <x-card title="Citas de hoy" :subtitle="$metrics['appointmentsToday'] . ' agendadas'">
            <div class="space-y-2">
                <p class="text-sm text-gray-600">No asistencias: {{ $metrics['noShows'] }}</p>
                <x-button variant="secondary" size="sm" href="{{ route('reservas.index') }}">Ver agenda completa</x-button>
            </div>
        </x-card>

        <x-card title="Accesos rápidos">
            <div class="flex flex-wrap gap-2">
                <x-button href="{{ route('users.admins.create') }}">Crear usuario</x-button>
                <x-button variant="secondary" href="{{ route('items.create') }}">Crear producto</x-button>
                <x-button variant="ghost" href="{{ route('kardex.index') }}">Ver reportes</x-button>
            </div>
        </x-card>
    </div>
@endsection
