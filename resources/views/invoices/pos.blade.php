@extends('layouts.app')

@section('content')
    <div x-data="posInvoice()" class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">POS - Facturación</h1>
                <p class="text-sm text-gray-500">Crea facturas rápidas con cálculos automáticos y control de inventario.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="ri-file-list-3-line"></i>
                    Ver facturas
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Hay errores en el formulario:</p>
                <ul class="mt-2 list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.store') }}" class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(360px,1fr)]">
            @csrf
            <div class="space-y-6">
                <div class="rounded-2xl border border-mint-100 bg-mint-50/60 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Cliente</h2>
                        <button type="button" class="rounded-lg bg-mint-600 px-3 py-1.5 text-xs text-white shadow-sm hover:bg-mint-700" style="background-color: var(--mint-600);">Crear cliente rápido</button>
                    </div>
                    <div class="mt-4">
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Selecciona un cliente</label>
                        <select id="owner_id" name="owner_id" class="mt-2 w-full" x-ref="ownerSelect" required>
                            <option value="">Buscar cliente...</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="rounded-2xl border border-mint-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Ítems</h2>
                        <button type="button" @click="addEmptyLine()" class="inline-flex items-center gap-2 rounded-lg bg-mint-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-mint-700" style="background-color: var(--mint-600);">
                            <i class="ri-add-line"></i>
                            Línea manual
                        </button>
                    </div>
                    <div class="mt-4">
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Buscar producto / servicio</label>
                        <div class="relative mt-2">
                            <input type="text" x-model="itemQuery" @input.debounce.300ms="searchItems" placeholder="Escribe para buscar..." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-mint-500 focus:ring-mint-500" />
                            <div x-show="itemResults.length" class="absolute z-10 mt-2 w-full rounded-xl border border-gray-100 bg-white shadow-lg">
                                <template x-for="item in itemResults" :key="item.id">
                                    <button type="button" @click="addItem(item)" class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-50">
                                        <span x-text="item.text"></span>
                                        <span class="text-xs text-gray-500" x-text="formatCurrency(item.price)"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-xl border border-gray-100">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-3 py-2 text-left">Descripción</th>
                                    <th class="px-3 py-2 text-center">Cantidad</th>
                                    <th class="px-3 py-2 text-right">Precio</th>
                                    <th class="px-3 py-2 text-right">Desc %</th>
                                    <th class="px-3 py-2 text-right">IVA %</th>
                                    <th class="px-3 py-2 text-right">Comisión %</th>
                                    <th class="px-3 py-2 text-right">Total</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(line, index) in lines" :key="line.uid">
                                    <tr class="bg-white">
                                        <td class="px-3 py-2">
                                            <input type="text" class="w-full rounded-md border border-gray-200 px-2 py-1 text-sm" x-model="line.description" :name="`lines[${index}][description]`" required>
                                            <input type="hidden" :name="`lines[${index}][item_id]`" :value="line.item_id">
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="line.quantity = Math.max(0.001, line.quantity - 1)" class="h-7 w-7 rounded-full border border-gray-200 text-gray-500">-</button>
                                                <input type="number" step="0.001" min="0.001" class="w-20 rounded-md border border-gray-200 px-2 py-1 text-center" x-model.number="line.quantity" :name="`lines[${index}][quantity]`">
                                                <button type="button" @click="line.quantity = line.quantity + 1" class="h-7 w-7 rounded-full border border-gray-200 text-gray-500">+</button>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input type="number" step="0.01" min="0" class="w-28 rounded-md border border-gray-200 px-2 py-1 text-right" x-model.number="line.unit_price" :name="`lines[${index}][unit_price]`">
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input type="number" step="0.01" min="0" max="100" class="w-20 rounded-md border border-gray-200 px-2 py-1 text-right" x-model.number="line.discount_rate" :name="`lines[${index}][discount_rate]`">
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input type="number" step="0.01" min="0" max="100" class="w-20 rounded-md border border-gray-200 px-2 py-1 text-right" x-model.number="line.tax_rate" :name="`lines[${index}][tax_rate]`">
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input type="number" step="0.01" min="0" max="100" class="w-20 rounded-md border border-gray-200 px-2 py-1 text-right" x-model.number="line.commission_rate" :name="`lines[${index}][commission_rate]`">
                                        </td>
                                        <td class="px-3 py-2 text-right font-semibold">
                                            <span x-text="formatCurrency(lineTotal(line))"></span>
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <button type="button" @click="removeLine(index)" class="text-red-500 hover:text-red-700">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="lines.length === 0">
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">Agrega productos o servicios para empezar.</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-2xl border border-mint-100 bg-gradient-to-br from-white via-white to-mint-50 p-5 shadow-sm sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900">Resumen</h2>
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium" x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Descuentos</span>
                            <span class="font-medium text-red-500" x-text="formatCurrency(discountTotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Impuestos</span>
                            <span class="font-medium" x-text="formatCurrency(taxTotal)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Comisiones</span>
                            <span class="font-medium" x-text="formatCurrency(commissionTotal)"></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-base">
                            <span class="font-semibold">Total</span>
                            <span class="font-semibold text-mint-700" x-text="formatCurrency(total)"></span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="rounded-xl border border-mint-100 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Venta a crédito</p>
                                    <p class="text-xs text-gray-500">Define el plazo de pago.</p>
                                </div>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" name="is_credit" value="1" x-model="is_credit" class="h-4 w-4 rounded border-gray-300 text-mint-600">
                                    Activar
                                </label>
                            </div>
                            <div class="mt-3" x-show="is_credit">
                                <label class="text-xs text-gray-500">Plazo</label>
                                <select name="credit_days" x-model.number="credit_days" class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm">
                                    <option value="5">5 días</option>
                                    <option value="10">10 días</option>
                                    <option value="15">15 días</option>
                                    <option value="30">30 días</option>
                                    <option value="60">60 días</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700">Pagos</h3>
                            <button type="button" @click="addPayment()" class="text-xs text-mint-700 hover:text-mint-900" :disabled="is_credit" :class="is_credit ? 'opacity-50 cursor-not-allowed' : ''">Agregar método</button>
                        </div>
                        <div x-show="is_credit" class="rounded-xl border border-mint-100 bg-mint-50 p-3 text-xs text-gray-600">
                            La factura quedará a crédito. No se registrarán pagos en este momento.
                        </div>
                        <template x-for="(payment, index) in payments" :key="payment.uid">
                            <div class="rounded-xl border border-mint-100 bg-white p-4 space-y-3" x-show="!is_credit">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <select class="min-w-[160px] rounded-md border border-gray-200 px-2 py-2 text-sm" x-model="payment.method" :name="`payments[${index}][method]`">
                                        <option value="cash">Efectivo</option>
                                        <option value="card">Tarjeta</option>
                                        <option value="transfer">Transferencia</option>
                                        <option value="mixed">Mixto</option>
                                    </select>
                                    <button type="button" @click="removePayment(index)" class="rounded-md bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">Quitar</button>
                                </div>
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="text-xs text-gray-500">Monto aplicado</label>
                                        <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model.number="payment.amount" :name="`payments[${index}][amount]`">
                                    </div>
                                    <template x-if="payment.method === 'card'">
                                        <div>
                                            <label class="text-xs text-gray-500">Tipo de tarjeta</label>
                                            <select class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model="payment.card_type" :name="`payments[${index}][card_type]`">
                                                <option value="debit">Débito</option>
                                                <option value="credit">Crédito</option>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="payment.method === 'cash'">
                                        <div>
                                            <label class="text-xs text-gray-500">Recibido</label>
                                            <input type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm" x-model.number="payment.received" :name="`payments[${index}][received]`">
                                            <p class="mt-2 text-xs text-gray-500">Devueltas: <span class="font-semibold" x-text="formatCurrency(paymentChange(payment))"></span></p>
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
                            </div>
                        </template>
                        <div class="rounded-xl bg-mint-50 p-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Pagado</span>
                                <span class="font-semibold" x-text="formatCurrency(paidTotal)"></span>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-gray-500">Devueltas</span>
                                <span class="font-semibold" x-text="formatCurrency(changeTotal)"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <button type="submit" class="w-full rounded-xl bg-mint-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-mint-700" style="background-color: var(--mint-600);">Cobrar / Finalizar</button>
                        <button type="button" class="w-full rounded-xl bg-mint-500/90 px-4 py-3 text-sm text-white shadow-sm hover:bg-mint-600" style="background-color: var(--mint-500);">Guardar borrador</button>
                    </div>
                </div>
            </aside>
        </form>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        function posInvoice() {
            return {
                itemQuery: '',
                itemResults: [],
                lines: [],
                is_credit: false,
                credit_days: 5,
                payments: [
                    { uid: crypto.randomUUID(), method: 'cash', amount: 0, received: 0, reference: '', card_type: 'debit', bank_id: '' }
                ],
                init() {
                    new TomSelect(this.$refs.ownerSelect, {
                        create: false,
                        placeholder: 'Buscar cliente...',
                        valueField: 'id',
                        labelField: 'text',
                        searchField: ['text', 'document', 'phone'],
                        load: (query, callback) => {
                            fetch(`{{ route('api.owners.search') }}?q=${encodeURIComponent(query)}`)
                                .then(response => response.json())
                                .then(data => callback(data))
                                .catch(() => callback());
                        }
                    });
                },
                searchItems() {
                    if (this.itemQuery.length < 2) {
                        this.itemResults = [];
                        return;
                    }
                    fetch(`{{ route('api.items.search') }}?q=${encodeURIComponent(this.itemQuery)}`)
                        .then(response => response.json())
                        .then(data => {
                            this.itemResults = data;
                        });

                    this.$watch('is_credit', (value) => {
                        if (value) {
                            this.payments = [];
                        } else if (this.payments.length === 0) {
                            this.addPayment();
                        }
                    });
                },
                addItem(item) {
                    this.lines.push({
                        uid: crypto.randomUUID(),
                        item_id: item.id,
                        description: item.text,
                        quantity: 1,
                        unit_price: Number(item.price || 0),
                        discount_rate: 0,
                        tax_rate: 0,
                        commission_rate: 0
                    });
                    this.itemQuery = '';
                    this.itemResults = [];
                },
                addEmptyLine() {
                    this.lines.push({
                        uid: crypto.randomUUID(),
                        item_id: '',
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        discount_rate: 0,
                        tax_rate: 0,
                        commission_rate: 0
                    });
                },
                removeLine(index) {
                    this.lines.splice(index, 1);
                },
                rateValue(rate) {
                    const value = Number(rate || 0);
                    return value > 1 ? value / 100 : value;
                },
                lineTotals(line) {
                    const lineSubtotal = Number(line.quantity || 0) * Number(line.unit_price || 0);
                    const discountAmount = lineSubtotal * this.rateValue(line.discount_rate);
                    const base = lineSubtotal - discountAmount;
                    const taxAmount = base * this.rateValue(line.tax_rate);
                    const commissionAmount = base * this.rateValue(line.commission_rate);
                    const lineTotal = base + taxAmount + commissionAmount;
                    return { lineSubtotal, discountAmount, taxAmount, commissionAmount, lineTotal };
                },
                lineTotal(line) {
                    return this.lineTotals(line).lineTotal;
                },
                get subtotal() {
                    return this.lines.reduce((sum, line) => sum + this.lineTotals(line).lineSubtotal, 0);
                },
                get discountTotal() {
                    return this.lines.reduce((sum, line) => sum + this.lineTotals(line).discountAmount, 0);
                },
                get taxTotal() {
                    return this.lines.reduce((sum, line) => sum + this.lineTotals(line).taxAmount, 0);
                },
                get commissionTotal() {
                    return this.lines.reduce((sum, line) => sum + this.lineTotals(line).commissionAmount, 0);
                },
                get total() {
                    return (this.subtotal - this.discountTotal) + this.taxTotal + this.commissionTotal;
                },
                addPayment() {
                    this.payments.push({ uid: crypto.randomUUID(), method: 'cash', amount: 0, received: 0, reference: '', card_type: 'debit', bank_id: '' });
                },
                removePayment(index) {
                    if (this.payments.length === 1) {
                        return;
                    }
                    this.payments.splice(index, 1);
                },
                paymentChange(payment) {
                    if (payment.method !== 'cash') {
                        return 0;
                    }
                    return Math.max(0, Number(payment.received || 0) - Number(payment.amount || 0));
                },
                get paidTotal() {
                    return this.payments.reduce((sum, payment) => sum + Number(payment.amount || 0), 0);
                },
                get changeTotal() {
                    return this.payments.reduce((sum, payment) => sum + this.paymentChange(payment), 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value || 0);
                }
            };
        }
    </script>
@endpush
