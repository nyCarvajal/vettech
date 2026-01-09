@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Informes rápidos</h1>
        <p class="text-sm text-gray-500">KPIs instantáneos para el periodo {{ $filters->rangeLabel() }}.</p>
    </div>

    <x-reports.filters :filters="$filters" :users="$users" :owners="$owners" :payment-methods="$paymentMethods" :show-granularity="false" :show-payment-method="true" />

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-reports.kpi-card label="Ventas" :value="'$' . number_format($sales['kpis']['total_sales'], 0, ',', '.')" hint="Total facturado" />
        <x-reports.kpi-card label="Pagos" :value="'$' . number_format($payments['kpis']['total_payments'], 0, ',', '.')" hint="Pagos recibidos" />
        <x-reports.kpi-card label="Comisiones" :value="'$' . number_format($sales['kpis']['commissions_total'], 0, ',', '.')" hint="Comisiones del periodo" />
        <x-reports.kpi-card label="Gastos" :value="'$' . number_format($expenses['kpis']['total_expenses'], 0, ',', '.')" hint="Egresos registrados" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <x-reports.chart-card title="Ventas diarias" chart-id="quickSalesChart" />
        <x-reports.chart-card title="Pagos diarios" chart-id="quickPaymentsChart" />
        <x-reports.chart-card title="Comisiones" chart-id="quickCommissionsChart" />
        <x-reports.chart-card title="Gastos" chart-id="quickExpensesChart" />
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('reports.sales', request()->query()) }}" class="border border-gray-300 rounded px-4 py-2">Ver detalle ventas</a>
        <a href="{{ route('reports.payments', request()->query()) }}" class="border border-gray-300 rounded px-4 py-2">Ver detalle pagos</a>
        <a href="{{ route('reports.expenses', request()->query()) }}" class="border border-gray-300 rounded px-4 py-2">Ver detalle gastos</a>
        <a href="{{ route('reports.cash', request()->query()) }}" class="border border-gray-300 rounded px-4 py-2">Ver caja</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const fetchQuickData = async () => {
        const params = new URLSearchParams(@json(request()->query()));
        const response = await fetch(`{{ route('reports.quick.data') }}?${params.toString()}`);
        return response.json();
    };

    const buildChart = (ctx, labels, data, label) => new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label,
                data,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
        },
    });

    fetchQuickData().then((data) => {
        const salesLabels = data.sales.map(item => item.label);
        const salesValues = data.sales.map(item => item.value);
        buildChart(document.getElementById('quickSalesChart'), salesLabels, salesValues, 'Ventas');

        const paymentsLabels = data.payments.map(item => item.label);
        const paymentsValues = data.payments.map(item => item.value);
        buildChart(document.getElementById('quickPaymentsChart'), paymentsLabels, paymentsValues, 'Pagos');

        const commissionsLabels = data.commissions.map(item => item.label);
        const commissionsValues = data.commissions.map(item => item.value);
        buildChart(document.getElementById('quickCommissionsChart'), commissionsLabels, commissionsValues, 'Comisiones');

        const expensesLabels = data.expenses.map(item => item.label);
        const expensesValues = data.expenses.map(item => item.value);
        buildChart(document.getElementById('quickExpensesChart'), expensesLabels, expensesValues, 'Gastos');
    });
</script>
@endpush
