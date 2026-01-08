@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-semibold text-gray-900">Factura {{ $invoice->full_number }}</h1>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : ($invoice->status === 'void' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">Emitida el {{ $invoice->issued_at?->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('invoices.print', $invoice) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600">Imprimir</a>
                @if($invoice->status !== 'void')
                    <form method="POST" action="{{ route('invoices.void', $invoice) }}">
                        @csrf
                        <button class="rounded-lg bg-red-600 px-3 py-2 text-sm text-white">Anular</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-700">Cliente</h2>
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $invoice->owner->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $invoice->owner->document ?? '' }}</p>
                <p class="text-sm text-gray-500">{{ $invoice->owner->phone ?? '' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-700">Totales</h2>
                <div class="mt-3 space-y-2 text-sm">
                    <div class="flex items-center justify-between"><span>Subtotal</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span>Descuentos</span><span>{{ number_format($invoice->discount_total, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span>Impuestos</span><span>{{ number_format($invoice->tax_total, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span>Comisiones</span><span>{{ number_format($invoice->commission_total, 2) }}</span></div>
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 font-semibold">
                        <span>Total</span><span>{{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-700">Pagos</h2>
                <div class="mt-3 space-y-2 text-sm">
                    @foreach($invoice->payments as $payment)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">{{ ucfirst($payment->method) }}</span>
                            <span class="font-semibold">{{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2">
                        <span>Total pagado</span><span class="font-semibold">{{ number_format($invoice->paid_total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Devueltas</span><span class="font-semibold">{{ number_format($invoice->change_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700">Líneas</h2>
            <div class="mt-4 overflow-hidden rounded-xl border border-gray-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Descripción</th>
                            <th class="px-4 py-2 text-center">Cantidad</th>
                            <th class="px-4 py-2 text-right">Precio</th>
                            <th class="px-4 py-2 text-right">Descuento</th>
                            <th class="px-4 py-2 text-right">Impuesto</th>
                            <th class="px-4 py-2 text-right">Comisión</th>
                            <th class="px-4 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoice->lines as $line)
                            <tr>
                                <td class="px-4 py-2">{{ $line->description }}</td>
                                <td class="px-4 py-2 text-center">{{ number_format($line->quantity, 3) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($line->unit_price, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($line->discount_amount, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($line->tax_amount, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($line->commission_amount, 2) }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ number_format($line->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
