@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Pagos y cartera</h1>
        <p class="text-sm text-gray-500">Resumen del periodo {{ $filters->rangeLabel() }}.</p>
    </div>

    <x-reports.filters :filters="$filters" :users="$users" :owners="$owners" :payment-methods="$paymentMethods" :show-payment-method="true" />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-reports.kpi-card label="Pagos recibidos" :value="'$' . number_format($data['kpis']['total_payments'], 0, ',', '.')" />
        <x-reports.kpi-card label="# Pagos" :value="number_format($data['kpis']['payments_count'], 0, ',', '.')" />
        <x-reports.kpi-card label="Cuentas por cobrar" :value="'$' . number_format($data['accounts_receivable']['total'], 0, ',', '.')" />
        <x-reports.kpi-card label="Facturas pendientes" :value="number_format($data['accounts_receivable']['count'], 0, ',', '.')" />
    </div>

    <x-reports.chart-card title="Pagos por periodo" chart-id="paymentsChart" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Pagos por método</h2>
            <x-reports.table :headers="['Método', 'Total']" :rows="$data['by_method']->map(fn($row) => [$row->label, '$' . number_format($row->value, 0, ',', '.')])" />
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Antigüedad de cartera</h2>
            <x-reports.table :headers="['Rango', 'Saldo']" :rows="$data['aging']->map(fn($row) => [$row->bucket, '$' . number_format($row->value, 0, ',', '.')])" />
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Top deudores</h2>
            <x-reports.table :headers="['Cliente', 'Saldo']" :rows="$data['top_debtors']->map(fn($row) => [$row->owner_name, '$' . number_format($row->balance, 0, ',', '.')])" />
        </div>
        @php
            $paymentRows = $data['table']->through(fn($row) => [
                \Carbon\Carbon::parse($row->paid_at)->format('Y-m-d'),
                $row->method,
                '$' . number_format($row->amount, 0, ',', '.'),
                $row->full_number,
                $row->owner_name,
            ]);
        @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Detalle de pagos</h2>
            <x-reports.table :headers="['Fecha', 'Método', 'Monto', 'Factura', 'Cliente']" :rows="$paymentRows" />
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'payments', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.payments.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(series => {
            const labels = series.map(item => item.label);
            const values = series.map(item => item.value);
            new Chart(document.getElementById('paymentsChart'), {
                type: 'bar',
                data: { labels, datasets: [{ data: values, backgroundColor: '#16a34a' }] },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        });
</script>
@endpush
