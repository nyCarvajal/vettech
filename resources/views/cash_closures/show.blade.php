@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl space-y-6 px-4 py-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detalle de cierre</h1>
            <p class="text-sm text-gray-500">{{ $closure->date->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cash.closures.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600">Volver</a>
            <a href="{{ route('cash.closures.print', $closure) }}" target="_blank" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Imprimir</a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-400">Total esperado</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">$ {{ number_format($closure->total_expected, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-400">Total contado</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">$ {{ number_format($closure->total_counted, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-400">Diferencia</p>
            <p class="mt-2 text-2xl font-semibold {{ $closure->difference == 0 ? 'text-emerald-600' : 'text-red-600' }}">$ {{ number_format($closure->difference, 2, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-400">Usuario</p>
            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $closure->user->nombre ?? $closure->user->name ?? 'Usuario #' . $closure->user_id }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Resumen por método</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-400">
                                <th class="pb-3">Método</th>
                                <th class="pb-3 text-right">Esperado</th>
                                <th class="pb-3 text-right">Contado</th>
                                <th class="pb-3 text-right">Diferencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr>
                                <td class="py-3">Efectivo</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->expected_cash, 2, ',', '.') }}</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->counted_cash, 2, ',', '.') }}</td>
                                <td class="py-3 text-right {{ ($closure->counted_cash - $closure->expected_cash) == 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    $ {{ number_format($closure->counted_cash - $closure->expected_cash, 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3">Tarjeta</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->expected_card ?? 0, 2, ',', '.') }}</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->counted_card ?? 0, 2, ',', '.') }}</td>
                                <td class="py-3 text-right {{ (($closure->counted_card ?? 0) - ($closure->expected_card ?? 0)) == 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    $ {{ number_format(($closure->counted_card ?? 0) - ($closure->expected_card ?? 0), 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3">Transferencia</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->expected_transfer ?? 0, 2, ',', '.') }}</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->counted_transfer ?? 0, 2, ',', '.') }}</td>
                                <td class="py-3 text-right {{ (($closure->counted_transfer ?? 0) - ($closure->expected_transfer ?? 0)) == 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    $ {{ number_format(($closure->counted_transfer ?? 0) - ($closure->expected_transfer ?? 0), 2, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="font-semibold">
                                <td class="py-3">Total</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->total_expected, 2, ',', '.') }}</td>
                                <td class="py-3 text-right">$ {{ number_format($closure->total_counted, 2, ',', '.') }}</td>
                                <td class="py-3 text-right {{ $closure->difference == 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    $ {{ number_format($closure->difference, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Movimientos del día</h2>
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600">Pagos</h3>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-gray-400">
                                        <th class="pb-3">Hora</th>
                                        <th class="pb-3">Cliente</th>
                                        <th class="pb-3">Método</th>
                                        <th class="pb-3 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    @forelse ($summary['payments'] as $payment)
                                        <tr>
                                            <td class="py-2">{{ $payment['time'] ?? '--' }}</td>
                                            <td class="py-2">{{ $payment['client'] }}</td>
                                            <td class="py-2 capitalize">{{ $payment['method'] }}</td>
                                            <td class="py-2 text-right">$ {{ number_format($payment['amount'], 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-sm text-gray-500">Sin pagos.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600">Gastos</h3>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-gray-400">
                                        <th class="pb-3">Hora</th>
                                        <th class="pb-3">Categoría</th>
                                        <th class="pb-3 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    @forelse ($summary['expenses']['items'] as $expense)
                                        <tr>
                                            <td class="py-2">{{ $expense['time'] ?? '--' }}</td>
                                            <td class="py-2">{{ $expense['category'] }}</td>
                                            <td class="py-2 text-right">$ {{ number_format($expense['amount'], 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 text-center text-sm text-gray-500">Sin gastos.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Notas</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $closure->notes ?: 'Sin observaciones.' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Resumen del día</h2>
                <div class="mt-3 space-y-2 text-sm text-gray-700">
                    <div class="flex items-center justify-between">
                        <span>Ingresos</span>
                        <span class="font-semibold">$ {{ number_format($summary['expected']['total'], 2, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Egresos</span>
                        <span class="font-semibold">$ {{ number_format($summary['expenses']['total'], 2, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Neto</span>
                        <span class="font-semibold">$ {{ number_format($summary['net'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
