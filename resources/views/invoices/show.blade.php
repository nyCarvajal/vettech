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
                <a href="{{ route('invoices.print', [$invoice, 'paper' => 'letter']) }}" target="_blank" rel="noopener" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600">Imprimir carta (PDF)</a>
                <a href="{{ route('invoices.print', [$invoice, 'paper' => 'ticket']) }}" target="_blank" rel="noopener" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600">Imprimir ticket 80mm</a>
                <a href="{{ route('invoices.pos', ['from_invoice' => $invoice->id]) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600">Editar en POS</a>
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


        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm" x-data="invoicePaymentsForm()">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-gray-700">Registrar pagos adicionales</h2>
                <button type="button" @click="addPayment()" class="rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">Agregar método</button>
            </div>
            @if($errors->any())
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="mt-4 space-y-3">
                @csrf
                <template x-for="(payment, index) in payments" :key="payment.uid">
                    <div class="rounded-xl border border-gray-100 p-3 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <select class="rounded-md border border-gray-200 px-2 py-2 text-sm" x-model="payment.method" :name="`payments[${index}][method]`">
                                <option value="cash">Efectivo</option>
                                <option value="transfer">Transferencia</option>
                                <option value="card">Banco (tarjeta)</option>
                            </select>
                            <button type="button" @click="removePayment(index)" class="rounded-md bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">Quitar</button>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Monto aplicado</label>
                            <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model.number="payment.amount" :name="`payments[${index}][amount]`" required>
                        </div>
                        <template x-if="payment.method === 'cash'">
                            <div>
                                <label class="text-xs text-gray-500">Recibido</label>
                                <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model.number="payment.received" :name="`payments[${index}][received]`">
                            </div>
                        </template>
                        <template x-if="payment.method === 'card'">
                            <div>
                                <label class="text-xs text-gray-500">Tipo de tarjeta</label>
                                <select class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model="payment.card_type" :name="`payments[${index}][card_type]`">
                                    <option value="debit">Débito</option>
                                    <option value="credit">Crédito</option>
                                </select>
                            </div>
                        </template>
                        <template x-if="payment.method === 'card' || payment.method === 'transfer'">
                            <div>
                                <label class="text-xs text-gray-500">Banco</label>
                                <select class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model="payment.bank_id" :name="`payments[${index}][bank_id]`">
                                    <option value="">Selecciona un banco</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </template>
                        <div>
                            <label class="text-xs text-gray-500">Referencia</label>
                            <input type="text" class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model="payment.reference" :name="`payments[${index}][reference]`">
                        </div>
                    </div>
                </template>

                <button type="submit" class="rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white hover:bg-mint-700" style="background-color: var(--mint-600);">
                    Guardar pagos
                </button>
            </form>
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

@push('scripts')
    <script>
        function invoicePaymentsForm() {
            return {
                payments: [
                    { uid: crypto.randomUUID(), method: 'cash', amount: 0, received: 0, reference: '', card_type: 'debit', bank_id: '' }
                ],
                addPayment() {
                    this.payments.push({ uid: crypto.randomUUID(), method: 'cash', amount: 0, received: 0, reference: '', card_type: 'debit', bank_id: '' });
                },
                removePayment(index) {
                    if (this.payments.length === 1) {
                        return;
                    }

                    this.payments.splice(index, 1);
                },
            };
        }
    </script>
@endpush

@endsection
