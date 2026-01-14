@extends('layouts.app')

@section('content')
<div
    x-data="{
        tab: 'payments',
        methodFilter: 'all',
        expectedCash: {{ $summary['expected']['cash'] }},
        expectedCard: {{ $summary['expected']['card'] }},
        expectedTransfer: {{ $summary['expected']['transfer'] }},
        countedCash: {{ old('counted_cash', $closure?->counted_cash ?? 0) }},
        countedCard: {{ old('counted_card', $closure?->counted_card ?? 0) }},
        countedTransfer: {{ old('counted_transfer', $closure?->counted_transfer ?? 0) }},
        format(value) {
            return Number(value || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        get totalExpected() {
            return this.expectedCash + this.expectedCard + this.expectedTransfer;
        },
        get totalCounted() {
            return this.countedCash + this.countedCard + this.countedTransfer;
        },
        get diffCash() {
            return this.countedCash - this.expectedCash;
        },
        get diffTotal() {
            return this.totalCounted - this.totalExpected;
        }
    }"
    class="mx-auto max-w-7xl space-y-6 px-4 py-6"
>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Arqueo de Caja</h1>
            <p class="text-sm text-gray-500">Control diario de ingresos, egresos y diferencias de caja.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('cash.closures.create') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $summary['date'] }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                <button class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600">Actualizar</button>
            </form>
            <a href="{{ route('cash.closures.create', ['date' => now()->toDateString()]) }}" class="rounded-lg bg-gray-100 px-3 py-2 text-sm">Hoy</a>
            <a href="{{ route('cash.closures.create', ['date' => now()->subDay()->toDateString()]) }}" class="rounded-lg bg-gray-100 px-3 py-2 text-sm">Ayer</a>
            <span class="rounded-full {{ $closure ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold">
                {{ $closure ? 'Cerrado' : 'Sin cierre' }}
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Revisa los datos:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        <div class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-400">Ingresos del día</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">$ {{ number_format($summary['expected']['total'], 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Efectivo, tarjeta y transferencias.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-400">Egresos del día</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">$ {{ number_format($summary['expenses']['total'], 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ $summary['expenses']['available'] ? 'Gastos registrados.' : 'Sin módulo de gastos.' }}</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-400">Neto del día</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">$ {{ number_format($summary['net'], 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Ingresos - egresos.</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-400">Pagos contados</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $summary['payment_counts']['payments'] }}</p>
                    <p class="text-xs text-gray-500">{{ $summary['payment_counts']['invoices'] }} facturas.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2">
                        <button @click="tab='payments'" :class="tab === 'payments' ? 'bg-mint-600 text-white' : 'bg-gray-100 text-gray-600'" class="rounded-full px-4 py-2 text-sm font-medium">Pagos</button>
                        <button @click="tab='expenses'" :class="tab === 'expenses' ? 'bg-mint-600 text-white' : 'bg-gray-100 text-gray-600'" class="rounded-full px-4 py-2 text-sm font-medium">Gastos</button>
                        <button @click="tab='summary'" :class="tab === 'summary' ? 'bg-mint-600 text-white' : 'bg-gray-100 text-gray-600'" class="rounded-full px-4 py-2 text-sm font-medium">Resumen por método</button>
                    </div>
                    <div x-show="tab === 'payments'" class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500">Filtrar:</span>
                        <select x-model="methodFilter" class="rounded-lg border border-gray-200 px-2 py-1">
                            <option value="all">Todos</option>
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </select>
                    </div>
                </div>

                <div x-show="tab === 'payments'" class="pt-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400">
                                    <th class="pb-3">Hora</th>
                                    <th class="pb-3">Cliente</th>
                                    <th class="pb-3">Factura</th>
                                    <th class="pb-3">Método</th>
                                    <th class="pb-3 text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($summary['payments'] as $payment)
                                    <tr x-show="methodFilter === 'all' || methodFilter === '{{ $payment['method'] }}'" class="text-gray-700">
                                        <td class="py-3">{{ $payment['time'] ?? '--' }}</td>
                                        <td class="py-3">{{ $payment['client'] }}</td>
                                        <td class="py-3">{{ $payment['invoice'] }}</td>
                                        <td class="py-3 capitalize">{{ $payment['method'] }}</td>
                                        <td class="py-3 text-right">$ {{ number_format($payment['amount'], 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-sm text-gray-500">No hay pagos registrados para esta fecha.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="tab === 'expenses'" class="pt-4">
                    @if (! $summary['expenses']['available'])
                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            No se encontró el módulo de egresos. Configura cash_movements para registrar gastos.
                        </div>
                    @endif
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400">
                                    <th class="pb-3">Hora</th>
                                    <th class="pb-3">Categoría</th>
                                    <th class="pb-3">Descripción</th>
                                    <th class="pb-3">Método</th>
                                    <th class="pb-3 text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($summary['expenses']['items'] as $expense)
                                    <tr class="text-gray-700">
                                        <td class="py-3">{{ $expense['time'] ?? '--' }}</td>
                                        <td class="py-3">{{ $expense['category'] }}</td>
                                        <td class="py-3">{{ $expense['description'] }}</td>
                                        <td class="py-3 capitalize">{{ $expense['method'] }}</td>
                                        <td class="py-3 text-right">$ {{ number_format($expense['amount'], 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-sm text-gray-500">No hay gastos registrados para esta fecha.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-show="tab === 'summary'" class="pt-4">
                    <div class="overflow-x-auto">
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
                                    <td class="py-3 text-right">$ {{ number_format($summary['expected']['cash'], 2, ',', '.') }}</td>
                                    <td class="py-3 text-right">$ <span x-text="format(countedCash)"></span></td>
                                    <td class="py-3 text-right" :class="diffCash === 0 ? 'text-emerald-600' : 'text-red-600'">
                                        <span x-text="format(diffCash)"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3">Tarjeta</td>
                                    <td class="py-3 text-right">$ {{ number_format($summary['expected']['card'], 2, ',', '.') }}</td>
                                    <td class="py-3 text-right">$ <span x-text="format(countedCard)"></span></td>
                                    <td class="py-3 text-right" :class="(countedCard - expectedCard) === 0 ? 'text-emerald-600' : 'text-red-600'">
                                        <span x-text="format(countedCard - expectedCard)"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3">Transferencia</td>
                                    <td class="py-3 text-right">$ {{ number_format($summary['expected']['transfer'], 2, ',', '.') }}</td>
                                    <td class="py-3 text-right">$ <span x-text="format(countedTransfer)"></span></td>
                                    <td class="py-3 text-right" :class="(countedTransfer - expectedTransfer) === 0 ? 'text-emerald-600' : 'text-red-600'">
                                        <span x-text="format(countedTransfer - expectedTransfer)"></span>
                                    </td>
                                </tr>
                                <tr class="font-semibold">
                                    <td class="py-3">Total</td>
                                    <td class="py-3 text-right">$ {{ number_format($summary['expected']['total'], 2, ',', '.') }}</td>
                                    <td class="py-3 text-right">$ <span x-text="format(totalCounted)"></span></td>
                                    <td class="py-3 text-right" :class="diffTotal === 0 ? 'text-emerald-600' : 'text-red-600'">
                                        <span x-text="format(diffTotal)"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="sticky top-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Cierre de Caja</h2>
                    <span class="text-xs text-gray-400">{{ $summary['date'] }}</span>
                </div>

                <form class="mt-4 space-y-4" method="POST" action="{{ route('cash.closures.store') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $summary['date'] }}">

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Esperado efectivo</span>
                            <span class="font-semibold">$ {{ number_format($summary['expected']['cash'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Contado efectivo</label>
                        <input type="number" step="0.01" name="counted_cash" x-model.number="countedCash" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" required>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Diferencia efectivo</span>
                            <span :class="diffCash === 0 ? 'text-emerald-600' : 'text-red-600'" class="font-semibold">$ <span x-text="format(diffCash)"></span></span>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Contado tarjeta</label>
                            <input type="number" step="0.01" name="counted_card" x-model.number="countedCard" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Contado transferencia</label>
                            <input type="number" step="0.01" name="counted_transfer" x-model.number="countedTransfer" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Total esperado</span>
                            <span class="font-semibold">$ {{ number_format($summary['expected']['total'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Total contado</span>
                            <span class="font-semibold">$ <span x-text="format(totalCounted)"></span></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Diferencia total</span>
                            <span :class="diffTotal === 0 ? 'text-emerald-600' : 'text-red-600'" class="font-semibold">$ <span x-text="format(diffTotal)"></span></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">{{ old('notes', $closure?->notes) }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Guardar cierre</button>
                        @if ($closure)
                            <a href="{{ route('cash.closures.print', $closure) }}" target="_blank" class="block w-full rounded-lg border border-gray-200 px-4 py-2 text-center text-sm font-semibold text-gray-600">Imprimir</a>
                        @endif
                    </div>

                    <div x-show="diffTotal !== 0" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                        Se detecta una diferencia en el cierre. Revisa los valores contados.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
