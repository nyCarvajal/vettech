@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Gastos y rentabilidad</h1>
        <p class="text-sm text-gray-500">Resumen del periodo {{ $filters->rangeLabel() }}.</p>
    </div>

    <x-reports.filters :filters="$filters" :owners="$owners" :show-user="false" />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <x-reports.kpi-card label="Gastos" :value="'$' . number_format($data['kpis']['total_expenses'], 0, ',', '.')" />
        <x-reports.kpi-card label="# Gastos" :value="number_format($data['kpis']['expenses_count'], 0, ',', '.')" />
        <x-reports.kpi-card label="Utilidad estimada" :value="'$' . number_format($data['kpis']['estimated_profit'], 0, ',', '.')" />
    </div>

    <x-reports.chart-card title="Gastos por periodo" chart-id="expensesChart" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Gastos por categoría</h2>
            <x-reports.table :headers="['Categoría', 'Total']" :rows="$data['by_category']->map(fn($row) => [$row->label, '$' . number_format($row->value, 0, ',', '.')])" />
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Margen por producto</h2>
            @if($data['margin'])
                <x-reports.table :headers="['Producto', 'Margen']" :rows="$data['margin']->map(fn($row) => [$row->item_name, '$' . number_format($row->margin, 0, ',', '.')])" />
            @else
                <p class="text-sm text-gray-500">No disponible: no existe costo por producto.</p>
            @endif
        </div>
    </div>

    @php
        $expenseRows = $data['table']->through(fn($row) => [
            \Carbon\Carbon::parse($row->paid_at)->format('Y-m-d'),
            $row->category,
            $row->description,
            '$' . number_format($row->amount, 0, ',', '.'),
            $row->payment_method,
            $row->owner_name,
        ]);
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <h2 class="font-semibold mb-3">Detalle de gastos</h2>
        <x-reports.table :headers="['Fecha', 'Categoría', 'Descripción', 'Monto', 'Método', 'Cliente']" :rows="$expenseRows" />
    </div>

    <div class="flex justify-between items-center">
        <a href="{{ route('expenses.index') }}" class="border border-gray-300 rounded px-4 py-2">Gestionar gastos</a>
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'expenses', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.expenses.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(series => {
            const labels = series.map(item => item.label);
            const values = series.map(item => item.value);
            new Chart(document.getElementById('expensesChart'), {
                type: 'bar',
                data: { labels, datasets: [{ data: values, backgroundColor: '#f97316' }] },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        });
</script>
@endpush
