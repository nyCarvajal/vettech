@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Operación y productividad</h1>
        <p class="text-sm text-gray-500">Servicios realizados en {{ $filters->rangeLabel() }}.</p>
    </div>

    <x-reports.filters :filters="$filters" :show-owner="false" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-reports.kpi-card label="Servicios realizados" :value="number_format($data['kpis']['services_count'], 0, ',', '.')" />
    </div>

    <x-reports.chart-card title="Servicios por periodo" chart-id="operationsChart" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Atenciones por usuario</h2>
            <x-reports.table :headers="['Usuario', 'Servicios']" :rows="$data['by_user']->map(fn($row) => [$row->user_name, $row->total])" />
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Horas pico</h2>
            @if($data['appointments'])
                <pre class="text-xs text-gray-600">{{ json_encode($data['appointments'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p class="text-sm text-gray-500">No disponible: no existe tabla de agenda/appointments.</p>
            @endif
        </div>
    </div>

    @php
        $operationRows = $data['table']->through(fn($row) => [
            \Carbon\Carbon::parse($row->issued_at)->format('Y-m-d'),
            $row->description,
            $row->quantity,
        ]);
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <h2 class="font-semibold mb-3">Detalle de servicios</h2>
        <x-reports.table :headers="['Fecha', 'Servicio', 'Cantidad']" :rows="$operationRows" />
    </div>

    <div class="flex justify-end">
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'operations', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.operations.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(series => {
            const labels = series.map(item => item.label);
            const values = series.map(item => item.value);
            new Chart(document.getElementById('operationsChart'), {
                type: 'line',
                data: { labels, datasets: [{ data: values, borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.1)', fill: true }] },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        });
</script>
@endpush
