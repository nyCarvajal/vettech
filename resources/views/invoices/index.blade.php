@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Facturas POS</h1>
                <p class="text-sm text-gray-500">Gestiona las facturas emitidas desde el punto de venta.</p>
            </div>
            <a href="{{ route('invoices.pos') }}" class="inline-flex items-center gap-2 rounded-lg bg-mint-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-mint-700">
                <i class="ri-add-line"></i>
                Nueva factura
            </a>
        </div>

        <form method="GET" class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-4 md:grid-cols-4">
            <div>
                <label class="text-xs font-semibold uppercase text-gray-500">Cliente</label>
                <select name="owner_id" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" @selected(request('owner_id') == $owner->id)>{{ $owner->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-500">Estado</label>
                <select name="status" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option value="">Todos</option>
                    @foreach(['draft' => 'Borrador', 'issued' => 'Emitida', 'paid' => 'Pagada', 'void' => 'Anulada'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-500">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-500">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-4 flex justify-end gap-2">
                <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Filtrar</button>
                <a href="{{ route('invoices.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600">Limpiar</a>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Factura</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $invoice->full_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $invoice->owner->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $invoice->issued_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($invoice->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badge = match($invoice->status) {
                                        'paid' => 'bg-green-100 text-green-700',
                                        'void' => 'bg-red-100 text-red-700',
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        default => 'bg-yellow-100 text-yellow-700',
                                    };
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-sm text-mint-700 hover:text-mint-900">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No hay facturas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $invoices->links() }}
    </div>
@endsection
