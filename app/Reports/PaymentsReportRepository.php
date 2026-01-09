<?php

namespace App\Reports;

class PaymentsReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters): array
    {
        $paymentsQuery = $this->basePaymentsQuery($filters);

        $totals = (clone $paymentsQuery)
            ->selectRaw('COALESCE(SUM(amount), 0) as total_payments')
            ->selectRaw('COUNT(*) as payments_count')
            ->first();

        $byMethod = (clone $paymentsQuery)
            ->selectRaw('method as label')
            ->selectRaw('COALESCE(SUM(amount), 0) as value')
            ->groupBy('method')
            ->orderByDesc('value')
            ->get();

        $series = (clone $paymentsQuery)
            ->selectRaw($this->dateGroupExpression('paid_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(amount), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $accountsReceivable = $this->accountsReceivable($filters);
        $aging = $this->agingBuckets($filters);
        $topDebtors = $this->topDebtors($filters);

        $tableQuery = (clone $paymentsQuery)
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_payments.invoice_id')
            ->leftJoin('owners', 'owners.id', '=', 'invoices.owner_id')
            ->select([
                'invoice_payments.paid_at',
                'invoice_payments.method',
                'invoice_payments.amount',
                'invoices.full_number',
                'owners.name as owner_name',
            ])
            ->orderByDesc('invoice_payments.paid_at');

        return [
            'kpis' => [
                'total_payments' => $totals->total_payments,
                'payments_count' => $totals->payments_count,
            ],
            'by_method' => $byMethod,
            'series' => $series,
            'table' => $tableQuery,
            'accounts_receivable' => $accountsReceivable,
            'aging' => $aging,
            'top_debtors' => $topDebtors,
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $rows = $this->basePaymentsQuery($filters)
            ->leftJoin('invoices', 'invoices.id', '=', 'invoice_payments.invoice_id')
            ->leftJoin('owners', 'owners.id', '=', 'invoices.owner_id')
            ->select([
                'invoice_payments.paid_at as fecha_pago',
                'invoice_payments.method as metodo',
                'invoice_payments.amount as monto',
                'invoices.full_number as factura',
                'owners.name as cliente',
            ])
            ->orderBy('invoice_payments.paid_at')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Fecha pago', 'Método', 'Monto', 'Factura', 'Cliente'],
            'rows' => $rows,
        ];
    }

    private function basePaymentsQuery(ReportFilters $filters)
    {
        $query = $this->connection()->table('invoice_payments');

        $this->applyDateRange($query, 'paid_at', $filters);
        $this->applyTenant($query, 'invoice_payments', $filters);

        if ($filters->paymentMethod) {
            $query->where('method', $filters->paymentMethod);
        }

        if ($filters->userId || $filters->ownerId) {
            $query->join('invoices as filter_invoices', 'filter_invoices.id', '=', 'invoice_payments.invoice_id');
            $this->applyOptionalFilters($query, $filters, 'filter_invoices.user_id', 'filter_invoices.owner_id');

            if ($filters->tenantId && $this->tableHasColumn('invoices', 'tenant_id')) {
                $query->where('filter_invoices.tenant_id', $filters->tenantId);
            }
        }

        return $query;
    }

    private function accountsReceivable(ReportFilters $filters): array
    {
        $query = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid'])
            ->whereColumn('paid_total', '<', 'total');

        $this->applyDateRange($query, 'issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);
        $this->applyOptionalFilters($query, $filters, 'invoices.user_id', 'invoices.owner_id');

        $total = (float) $query->sum($this->connection()->raw('total - paid_total'));
        $count = (int) $query->count();

        return [
            'total' => $total,
            'count' => $count,
        ];
    }

    private function agingBuckets(ReportFilters $filters)
    {
        $dateColumn = $this->tableHasColumn('invoices', 'due_date') ? 'due_date' : 'issued_at';

        $query = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid'])
            ->whereColumn('paid_total', '<', 'total');

        $this->applyDateRange($query, $dateColumn, $filters);
        $this->applyTenant($query, 'invoices', $filters);

        $driver = $this->connection()->getDriverName();
        $diffExpression = $driver === 'sqlite'
            ? "CAST((julianday('now') - julianday({$dateColumn})) AS INTEGER)"
            : "DATEDIFF(CURDATE(), {$dateColumn})";

        return $query
            ->selectRaw("CASE
                WHEN {$diffExpression} BETWEEN 0 AND 30 THEN '0-30'
                WHEN {$diffExpression} BETWEEN 31 AND 60 THEN '31-60'
                WHEN {$diffExpression} BETWEEN 61 AND 90 THEN '61-90'
                ELSE '90+'
            END as bucket")
            ->selectRaw('COALESCE(SUM(total - paid_total), 0) as value')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();
    }

    private function topDebtors(ReportFilters $filters)
    {
        $query = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid'])
            ->whereColumn('paid_total', '<', 'total')
            ->leftJoin('owners', 'owners.id', '=', 'invoices.owner_id');

        $this->applyDateRange($query, 'issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);

        return $query
            ->selectRaw('owners.name as owner_name')
            ->selectRaw('COALESCE(SUM(total - paid_total), 0) as balance')
            ->groupBy('owners.name')
            ->orderByDesc('balance')
            ->limit(10)
            ->get();
    }
}
