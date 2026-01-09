<?php

namespace App\Reports;

class CashReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters): array
    {
        $paymentsQuery = $this->connection()->table('invoice_payments');
        $expensesQuery = $this->connection()->table('expenses');

        $this->applyDateRange($paymentsQuery, 'paid_at', $filters);
        $this->applyDateRange($expensesQuery, 'paid_at', $filters);

        $this->applyTenant($paymentsQuery, 'invoice_payments', $filters);
        $this->applyTenant($expensesQuery, 'expenses', $filters);

        if ($filters->paymentMethod) {
            $paymentsQuery->where('method', $filters->paymentMethod);
        }

        $incomeTotal = (float) $paymentsQuery->sum('amount');
        $expenseTotal = (float) $expensesQuery->sum('amount');

        $seriesQuery = $this->connection()->table('invoice_payments')
            ->selectRaw($this->dateGroupExpression('paid_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(amount), 0) as income')
            ->whereBetween('paid_at', [$filters->from, $filters->to])
            ->groupBy('label')
            ->orderBy('label');
        $this->applyTenant($seriesQuery, 'invoice_payments', $filters);

        $series = $seriesQuery->get();

        $expenseSeriesQuery = $this->connection()->table('expenses')
            ->selectRaw($this->dateGroupExpression('paid_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(amount), 0) as expenses')
            ->whereBetween('paid_at', [$filters->from, $filters->to])
            ->groupBy('label')
            ->orderBy('label');
        $this->applyTenant($expenseSeriesQuery, 'expenses', $filters);

        $expenseSeries = $expenseSeriesQuery->get();

        $movements = $this->cashMovementsTable($filters);

        return [
            'kpis' => [
                'income_total' => $incomeTotal,
                'expense_total' => $expenseTotal,
                'net_total' => $incomeTotal - $expenseTotal,
            ],
            'series' => $series,
            'expense_series' => $expenseSeries,
            'table' => $movements,
            'closures' => $this->cashClosures($filters),
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $rows = $this->cashMovementsTable($filters)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Fecha', 'Tipo', 'Método', 'Descripción', 'Monto'],
            'rows' => $rows,
        ];
    }

    private function cashMovementsTable(ReportFilters $filters)
    {
        $payments = $this->connection()->table('invoice_payments')
            ->selectRaw("paid_at as date, 'Ingreso' as type, method as method, '' as description, amount as amount")
            ->whereBetween('paid_at', [$filters->from, $filters->to]);

        $expenses = $this->connection()->table('expenses')
            ->selectRaw("paid_at as date, 'Egreso' as type, payment_method as method, description as description, amount as amount")
            ->whereBetween('paid_at', [$filters->from, $filters->to]);

        $this->applyTenant($payments, 'invoice_payments', $filters);
        $this->applyTenant($expenses, 'expenses', $filters);

        $query = $payments->unionAll($expenses);

        return $this->connection()->query()->fromSub($query, 'movements')
            ->orderByDesc('date');
    }

    private function cashClosures(ReportFilters $filters)
    {
        if (! $this->connection()->getSchemaBuilder()->hasTable('cash_closures')) {
            return collect();
        }

        $query = $this->connection()->table('cash_closures')
            ->whereBetween('date', [$filters->from->toDateString(), $filters->to->toDateString()])
            ->orderByDesc('date');

        $this->applyTenant($query, 'cash_closures', $filters);

        return $query->get();
    }
}
