<?php

namespace App\Reports;

class ExpensesReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters): array
    {
        $expensesQuery = $this->baseExpensesQuery($filters);

        $totals = (clone $expensesQuery)
            ->selectRaw('COALESCE(SUM(amount), 0) as total_expenses')
            ->selectRaw('COUNT(*) as expenses_count')
            ->first();

        $series = (clone $expensesQuery)
            ->selectRaw($this->dateGroupExpression('paid_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(amount), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $byCategory = (clone $expensesQuery)
            ->selectRaw('category as label')
            ->selectRaw('COALESCE(SUM(amount), 0) as value')
            ->groupBy('category')
            ->orderByDesc('value')
            ->get();

        $tableQuery = (clone $expensesQuery)
            ->leftJoin('owners', 'owners.id', '=', 'expenses.owner_id')
            ->select([
                'expenses.paid_at',
                'expenses.category',
                'expenses.description',
                'expenses.amount',
                'expenses.payment_method',
                'owners.name as owner_name',
            ])
            ->orderByDesc('expenses.paid_at');

        $salesQuery = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid'])
            ->whereBetween('issued_at', [$filters->from, $filters->to]);
        $this->applyTenant($salesQuery, 'invoices', $filters);

        $salesTotal = (float) $salesQuery->sum('total');

        $profit = $salesTotal - (float) $totals->total_expenses;

        $marginByProduct = $this->marginByProduct($filters);

        return [
            'kpis' => [
                'total_expenses' => $totals->total_expenses,
                'expenses_count' => $totals->expenses_count,
                'estimated_profit' => $profit,
            ],
            'series' => $series,
            'by_category' => $byCategory,
            'table' => $tableQuery,
            'margin' => $marginByProduct,
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $rows = $this->baseExpensesQuery($filters)
            ->leftJoin('owners', 'owners.id', '=', 'expenses.owner_id')
            ->select([
                'expenses.paid_at as fecha_pago',
                'expenses.category as categoria',
                'expenses.description as descripcion',
                'expenses.amount as monto',
                'expenses.payment_method as metodo',
                'owners.name as cliente',
            ])
            ->orderBy('expenses.paid_at')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Fecha', 'Categoría', 'Descripción', 'Monto', 'Método', 'Cliente'],
            'rows' => $rows,
        ];
    }

    private function baseExpensesQuery(ReportFilters $filters)
    {
        $query = $this->connection()->table('expenses');

        $this->applyDateRange($query, 'paid_at', $filters);
        $this->applyTenant($query, 'expenses', $filters);

        return $query;
    }

    private function marginByProduct(ReportFilters $filters)
    {
        $hasCost = $this->tableHasColumn('items', 'cost_price') || $this->tableHasColumn('items', 'costo');
        if (! $hasCost) {
            return null;
        }

        $nameColumn = $this->resolveItemNameColumn();
        $costColumn = $this->tableHasColumn('items', 'cost_price') ? 'items.cost_price' : 'items.costo';

        $query = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_lines.item_id')
            ->whereIn('invoices.status', ['issued', 'paid']);

        $this->applyDateRange($query, 'invoices.issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);

        return $query
            ->selectRaw("{$nameColumn} as item_name")
            ->selectRaw("COALESCE(SUM((invoice_lines.unit_price - {$costColumn}) * invoice_lines.quantity), 0) as margin")
            ->groupBy('item_name')
            ->orderByDesc('margin')
            ->limit(10)
            ->get();
    }
}
