@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Ventas y facturación</h1>
        <p class="text-sm text-gray-500">Detalle del periodo {{ $filters->rangeLabel() }}.</p>
    </div>

    <x-reports.filters :filters="$filters" :users="$users" :owners="$owners" />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-reports.kpi-card label="Ventas" :value="'$' . number_format($data['kpis']['total_sales'], 0, ',', '.')" />
        <x-reports.kpi-card label="Ticket promedio" :value="'$' . number_format($data['kpis']['avg_ticket'], 0, ',', '.')" />
        <x-reports.kpi-card label="Comisiones" :value="'$' . number_format($data['kpis']['commissions_total'], 0, ',', '.')" />
        <x-reports.kpi-card label="# Facturas" :value="number_format($data['kpis']['invoices_count'], 0, ',', '.')" />
    </div>

    <x-reports.chart-card title="Ventas por periodo" chart-id="salesChart" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Ventas por servicio</h2>
            <x-reports.table :headers="['Servicio', 'Cantidad', 'Total']" :rows="$data['services']->map(fn($row) => [$row->service_type, $row->qty, '$' . number_format($row->total, 0, ',', '.')])" />
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Top productos</h2>
            <x-reports.table :headers="['Producto', 'Cantidad', 'Total']" :rows="$data['products']->map(fn($row) => [$row->item_name, $row->qty, '$' . number_format($row->total, 0, ',', '.')])" />
        </div>
    </div>

    @php
        $invoiceRows = $data['table']->through(fn($row) => [
            $row->full_number,
            $row->owner_name,
            \Carbon\Carbon::parse($row->issued_at)->format('Y-m-d'),
            $row->status,
            '$' . number_format($row->total, 0, ',', '.'),
            '$' . number_format($row->paid_total, 0, ',', '.'),
        ]);
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <h2 class="font-semibold mb-3">Facturas emitidas</h2>
        <x-reports.table :headers="['Factura', 'Cliente', 'Fecha', 'Estado', 'Total', 'Pagado']" :rows="$invoiceRows" />
    </div>

    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">Comparativo mensual: {{ '$' . number_format($data['comparison']['current'], 0, ',', '.') }} vs {{ '$' . number_format($data['comparison']['previous'], 0, ',', '.') }}.
            @if($data['comparison']['variation'] !== null)
                Variación: {{ number_format($data['comparison']['variation'], 1) }}%
            @endif
        </div>
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'sales', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.sales.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(series => {
            const labels = series.map(item => item.label);
            const values = series.map(item => item.value);
            new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: { labels, datasets: [{ data: values, borderColor: '#16a34a', backgroundColor: 'rgba(22, 163, 74, 0.1)', fill: true, tension: 0.3 }] },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        });
</script>
@endpush
