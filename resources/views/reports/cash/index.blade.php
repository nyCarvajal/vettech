@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Caja y arqueo</h1>
        <p class="text-sm text-gray-500">Flujos de caja para {{ $filters->rangeLabel() }}.</p>
    </div>

    <x-reports.filters :filters="$filters" :show-user="false" :show-owner="false" :payment-methods="$paymentMethods" :show-payment-method="true" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-reports.kpi-card label="Ingresos" :value="'$' . number_format($data['kpis']['income_total'], 0, ',', '.')" />
        <x-reports.kpi-card label="Egresos" :value="'$' . number_format($data['kpis']['expense_total'], 0, ',', '.')" />
        <x-reports.kpi-card label="Balance" :value="'$' . number_format($data['kpis']['net_total'], 0, ',', '.')" />
    </div>

    <x-reports.chart-card title="Ingresos vs egresos" chart-id="cashChart" />

    @php
        $movementRows = $data['table']->through(fn($row) => [
            \Carbon\Carbon::parse($row->date)->format('Y-m-d'),
            $row->type,
            $row->method,
            $row->description,
            '$' . number_format($row->amount, 0, ',', '.'),
        ]);
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
        <h2 class="font-semibold mb-3">Movimientos de caja</h2>
        <x-reports.table :headers="['Fecha', 'Tipo', 'Método', 'Descripción', 'Monto']" :rows="$movementRows" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Registrar arqueo</h2>
            <form method="POST" action="{{ route('reports.cash.closures.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-sm text-gray-600">Fecha</label>
                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Efectivo esperado</label>
                    <input type="number" step="0.01" name="expected_cash" class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Efectivo contado</label>
                    <input type="number" step="0.01" name="counted_cash" class="w-full border border-gray-300 rounded px-3 py-2" />
                </div>
                <div>
                    <label class="text-sm text-gray-600">Notas</label>
                    <textarea name="notes" class="w-full border border-gray-300 rounded px-3 py-2" rows="3"></textarea>
                </div>
                <button type="submit" class="bg-mint-600 text-white px-4 py-2 rounded">Guardar arqueo</button>
            </form>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Arqueos registrados</h2>
            <x-reports.table :headers="['Fecha', 'Esperado', 'Contado', 'Diferencia']" :rows="$data['closures']->map(fn($row) => [
                \Carbon\Carbon::parse($row->date)->format('Y-m-d'),
                '$' . number_format($row->expected_cash, 0, ',', '.'),
                '$' . number_format($row->counted_cash, 0, ',', '.'),
                '$' . number_format($row->difference, 0, ',', '.'),
            ])" />
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('reports.export', array_merge(request()->query(), ['report' => 'cash', 'format' => 'csv'])) }}" class="border border-gray-300 rounded px-4 py-2">Exportar CSV</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const params = new URLSearchParams(@json(request()->query()));
    fetch(`{{ route('reports.cash.data') }}?${params.toString()}`)
        .then(response => response.json())
        .then(({ income, expenses }) => {
            const labels = Array.from(new Set([...income.map(i => i.label), ...expenses.map(i => i.label)])).sort();
            const incomeMap = new Map(income.map(item => [item.label, item.value]));
            const expenseMap = new Map(expenses.map(item => [item.label, item.value]));
            new Chart(document.getElementById('cashChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Ingresos', data: labels.map(l => incomeMap.get(l) || 0), borderColor: '#16a34a', backgroundColor: 'rgba(22, 163, 74, 0.1)', fill: false },
                        { label: 'Egresos', data: labels.map(l => expenseMap.get(l) || 0), borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: false },
                    ],
                },
                options: { responsive: true },
            });
        });
</script>
@endpush
