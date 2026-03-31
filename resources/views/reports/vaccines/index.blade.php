@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Informe de vacunas</h1>
        <p class="text-sm text-gray-500">Consulta vacunas con todos los datos del tutor principal, la mascota y la aplicación.</p>
    </div>

    <div class="space-y-4">
        <x-reports.filters :filters="$filters"  :owners="$owners" />

        <form method="GET" class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="range" value="{{ request('range', '30d') }}" />
                <input type="hidden" name="from" value="{{ $filters->from->format('Y-m-d') }}" />
                <input type="hidden" name="to" value="{{ $filters->to->format('Y-m-d') }}" />
                <input type="hidden" name="granularity" value="{{ $filters->granularity }}" />
              
                <input type="hidden" name="owner_id" value="{{ request('owner_id') }}" />

                <div class="flex flex-col gap-1">
                    <label class="text-sm text-gray-600">Buscar</label>
                    <input type="text" name="q" value="{{ $extraFilters['q'] }}" placeholder="Mascota, tutor, vacuna o lote" class="border border-gray-300 rounded px-3 py-2 min-w-[260px]" />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm text-gray-600">Rabia</label>
                    <select name="rabies" class="border border-gray-300 rounded px-3 py-2">
                        <option value="all" @selected($extraFilters['rabies'] === 'all')>Todas</option>
                        <option value="yes" @selected($extraFilters['rabies'] === 'yes')>Con rabia</option>
                        <option value="no" @selected($extraFilters['rabies'] === 'no')>Sin rabia</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm text-gray-600">Origen</label>
                    <select name="source" class="border border-gray-300 rounded px-3 py-2">
                        <option value="all" @selected($extraFilters['source'] === 'all')>Todos</option>
                        <option value="inventory" @selected($extraFilters['source'] === 'inventory')>Inventario</option>
                        <option value="manual" @selected($extraFilters['source'] === 'manual')>Escrita a mano</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-mint-600 text-white px-4 py-2 rounded">Aplicar</button>
                    <a href="{{ route('reports.vaccines') }}" class="border border-gray-300 px-4 py-2 rounded">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-reports.kpi-card label="Vacunas registradas" :value="number_format($data['kpis']['total_vaccines'], 0, ',', '.')" />
        <x-reports.kpi-card label="Con rabia" :value="number_format($data['kpis']['rabies_count'], 0, ',', '.')" />
        <x-reports.kpi-card label="Desde inventario" :value="number_format($data['kpis']['inventory_count'], 0, ',', '.')" />
        <x-reports.kpi-card label="Escritas a mano" :value="number_format($data['kpis']['manual_count'], 0, ',', '.')" />
    </div>
    

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <x-reports.chart-card title="Aplicaciones por periodo" chart-id="vaccinesSeriesChart" />
        <x-reports.chart-card title="Estado de vacunas" chart-id="vaccinesStatusChart" />
    </div>

    @php
        $rows = $data['table']->through(fn($row) => [
            $row->applied_at ? \Carbon\Carbon::parse($row->applied_at)->format('Y-m-d') : 'N/D',
            $row->vaccine_name,
            $row->contains_rabies ? 'Sí' : 'No',
            $row->source_label,
            $row->patient_name,
            $row->owner_name ?: 'Sin tutor principal',
            $row->inventory_item_name ?: $row->manual_item_name ?: 'N/D',
            $row->batch_lot,
            $row->status_label,
        ]);
    @endphp

    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
            <div>
                <h2 class="font-semibold">Detalle de vacunas</h2>
                <p class="text-sm text-gray-500">Incluye tutor principal, mascota y trazabilidad de la vacuna.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'vaccines', 'format' => 'excel'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar Excel</a>
                <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'vaccines', 'format' => 'pdf'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar PDF</a>
            </div>
        </div>

        <x-reports.table :headers="['Fecha', 'Vacuna', 'Rabia', 'Origen', 'Mascota', 'Tutor principal', 'Producto', 'Lote', 'Estado']" :rows="$rows" />
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.vaccines.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(payload => {
            new Chart(document.getElementById('vaccinesSeriesChart'), {
                type: 'bar',
                data: {
                    labels: payload.series.map(item => item.label),
                    datasets: [{
                        data: payload.series.map(item => item.value),
                        backgroundColor: '#10b981',
                        borderRadius: 8,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });

            new Chart(document.getElementById('vaccinesStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: payload.statusBreakdown.map(item => item.label),
                    datasets: [{
                        data: payload.statusBreakdown.map(item => item.value),
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                    }]
                },
                options: { responsive: true },
            });
        });
</script>
@endpush
