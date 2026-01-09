@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Inventario</h1>
        <p class="text-sm text-gray-500">Kardex y rotación de productos.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-4">
        <x-reports.filters :filters="$filters" :show-user="false" :show-owner="false" />
        <form method="GET" class="flex items-end gap-3">
            <input type="hidden" name="range" value="{{ request('range') }}" />
            <input type="hidden" name="from" value="{{ $filters->from->format('Y-m-d') }}" />
            <input type="hidden" name="to" value="{{ $filters->to->format('Y-m-d') }}" />
            <input type="hidden" name="granularity" value="{{ $filters->granularity }}" />
            <div>
                <label class="text-sm text-gray-600">Item ID</label>
                <input type="number" name="item_id" value="{{ $itemId }}" class="border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="border border-gray-300 rounded px-4 py-2">Filtrar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-reports.kpi-card label="Movimientos" :value="number_format($data['kpis']['movements_count'], 0, ',', '.')" />
        <x-reports.kpi-card label="Valorización" :value="$data['valuation'] !== null ? '$' . number_format($data['valuation'], 0, ',', '.') : 'No disponible'" />
    </div>

    <x-reports.chart-card title="Movimientos por periodo" chart-id="inventoryChart" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Stock bajo</h2>
            <x-reports.table :headers="['Item', 'Stock', 'Mínimo']" :rows="$data['low_stock']->map(fn($row) => [$row->item_name, $row->stock, $row->minimum])" />
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Rotación (top salidas)</h2>
            <x-reports.table :headers="['Item', 'Cantidad']" :rows="$data['rotation']->map(fn($row) => [$row->item_name, $row->qty])" />
        </div>
    </div>

    @php
        $inventoryRows = $data['table']->through(fn($row) => [
            \Carbon\Carbon::parse($row->occurred_at)->format('Y-m-d'),
            $row->movement_type,
            $row->quantity,
            $row->item_name,
        ]);
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <h2 class="font-semibold mb-3">Kardex</h2>
        <x-reports.table :headers="['Fecha', 'Tipo', 'Cantidad', 'Item']" :rows="$inventoryRows" />
    </div>

    <div class="flex justify-end">
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'inventory', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.inventory.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(series => {
            const labels = series.map(item => item.label);
            const values = series.map(item => item.value);
            new Chart(document.getElementById('inventoryChart'), {
                type: 'bar',
                data: { labels, datasets: [{ data: values, backgroundColor: '#6366f1' }] },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        });
</script>
@endpush
