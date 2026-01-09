@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Peluquería</h1>
        <p class="text-sm text-gray-500">Indicadores de servicios de peluquería.</p>
    </div>

    <x-reports.filters :filters="$filters" :show-owner="false" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-reports.kpi-card label="Ingresos" :value="'$' . number_format($data['kpis']['grooming_total'], 0, ',', '.')" />
        <x-reports.kpi-card label="Servicios" :value="number_format($data['kpis']['services_count'], 0, ',', '.')" />
    </div>

    <x-reports.chart-card title="Ingresos de peluquería" chart-id="groomingChart" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Servicios más vendidos</h2>
            <x-reports.table :headers="['Servicio', 'Cantidad', 'Total']" :rows="$data['top_services']->map(fn($row) => [$row->service, $row->qty, '$' . number_format($row->total, 0, ',', '.')])" />
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Clientes frecuentes</h2>
            <x-reports.table :headers="['Cliente', 'Servicios']" :rows="$data['top_clients']->map(fn($row) => [$row->owner_name, $row->total_services])" />
        </div>
    </div>

    @php
        $groomingRows = $data['table']->through(fn($row) => [
            \Carbon\Carbon::parse($row->issued_at)->format('Y-m-d'),
            $row->description,
            $row->quantity,
            '$' . number_format($row->line_total, 0, ',', '.'),
        ]);
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <h2 class="font-semibold mb-3">Detalle de servicios</h2>
        <x-reports.table :headers="['Fecha', 'Servicio', 'Cantidad', 'Total']" :rows="$groomingRows" />
    </div>

    <div class="flex justify-end">
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'grooming', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.grooming.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(series => {
            const labels = series.map(item => item.label);
            const values = series.map(item => item.value);
            new Chart(document.getElementById('groomingChart'), {
                type: 'line',
                data: { labels, datasets: [{ data: values, borderColor: '#ec4899', backgroundColor: 'rgba(236, 72, 153, 0.1)', fill: true }] },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        });
</script>
@endpush
