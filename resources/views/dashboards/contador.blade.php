@extends('layouts.app')

@section('content')
    @include('partials.page-header', [
        'title' => 'Dashboard contador',
        'subtitle' => 'Control financiero y cierres',
    ])

    <div class="mt-4">
        <form class="flex flex-col md:flex-row md:items-end gap-3" method="GET">
            <div class="w-full md:w-40">
                <x-select label="Rango rápido" name="preset">
                    <option value="hoy" @selected(($filter['preset'] ?? '') === 'hoy')>Hoy</option>
                    <option value="7d" @selected(($filter['preset'] ?? '') === '7d')>Últimos 7 días</option>
                    <option value="30d" @selected(($filter['preset'] ?? '') === '30d')>Últimos 30 días</option>
                </x-select>
            </div>
            <div class="flex flex-1 gap-3">
                <x-input class="w-full" type="date" label="Desde" name="date_from" :value="$filter['date_from']" />
                <x-input class="w-full" type="date" label="Hasta" name="date_to" :value="$filter['date_to']" />
            </div>
            <x-button class="md:self-start" type="submit">Aplicar</x-button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <x-kpi label="Ventas pagadas" :value="'$' . number_format($metrics['sales']->sum('total'), 0, ',', '.')"
               :hint="$metrics['sales']->map(fn ($sale) => ucfirst($sale->method) . ': $' . number_format($sale->total, 0, ',', '.'))->implode(' | ')" />
        <x-kpi label="Egresos del periodo" :value="'$' . number_format($metrics['expenses']->sum('amount'), 0, ',', '.')" hint="Top 10 mostrados abajo" />
        <x-kpi label="Ventas anuladas" :value="$metrics['voidSales']->count()" hint="Listado de las últimas 10" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <x-card :title="'Caja por turno (' . $filter['date_from'] . ' - ' . $filter['date_to'] . ')'">
            <x-table :headers="['Turno', 'Esperado', 'Contado', 'Diferencia', 'Estado']" :empty="$metrics['cashSessions']->isEmpty()" empty-message="Sin sesiones en el rango">
                @foreach ($metrics['cashSessions'] as $session)
                    @php
                        $difference = ($session->closing_amount_counted ?? 0) - ($session->closing_amount_expected ?? 0);
                        $status = $difference === 0 ? 'OK' : 'Diferencia';
                        $variant = $difference === 0 ? 'mint' : 'danger';
                    @endphp
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ optional($session->opened_at)->format('H:i') }} - {{ optional($session->closed_at)->format('H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">${{ number_format($session->closing_amount_expected ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">${{ number_format($session->closing_amount_counted ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">${{ number_format($difference, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700"><x-badge :variant="$variant" :text="$status" /></td>
                    </tr>
                @endforeach
            </x-table>
        </x-card>

        <x-card title="Egresos del día">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['expenses'] as $expense)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">${{ number_format($expense->amount, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">{{ $expense->reason ?? 'Movimiento' }}</p>
                        </div>
                        <x-badge variant="gray" :text="optional($expense->created_at)->format('d/m H:i')" />
                    </div>
                @empty
                    <x-empty title="Sin egresos" description="No hay movimientos en el rango seleccionado"></x-empty>
                @endforelse
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
        <x-card title="Ventas anuladas">
            <div class="divide-y divide-gray-100">
                @forelse ($metrics['voidSales'] as $sale)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">Venta #{{ $sale->id }}</p>
                            <p class="text-sm text-gray-500">Total: ${{ number_format($sale->total ?? 0, 0, ',', '.') }} · {{ optional($sale->created_at)->format('d/m H:i') }}</p>
                        </div>
                        <x-badge variant="danger" text="Anulada" />
                    </div>
                @empty
                    <x-empty title="Sin anulaciones" description="No hay ventas anuladas en el periodo"></x-empty>
                @endforelse
            </div>
        </x-card>

        @php
            $cajasUrl = Route::has('cajas.index') ? route('cajas.index') : '#';
            $ventasUrl = Route::has('ventas.index') ? route('ventas.index') : '#';
            $salidasUrl = Route::has('salidas.index') ? route('salidas.index') : '#';
        @endphp

        <x-card title="Exportables">
            <div class="flex flex-wrap gap-2 mb-4">
                <x-button variant="secondary" size="sm" href="#">Exportar Excel</x-button>
                <x-button variant="ghost" size="sm" href="#">Exportar CSV</x-button>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button href="{{ $cajasUrl }}">Revisar cierres pendientes</x-button>
                <x-button variant="secondary" href="{{ $ventasUrl }}">Ver ventas por rango</x-button>
                <x-button variant="ghost" href="{{ $salidasUrl }}">Exportar movimientos</x-button>
            </div>
        </x-card>
    </div>
@endsection
