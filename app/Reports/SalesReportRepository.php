<?php

namespace App\Reports;

use Illuminate\Database\Query\Builder;

class SalesReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters): array
    {
        $invoiceQuery = $this->baseInvoiceQuery($filters);

        $totals = (clone $invoiceQuery)
            ->selectRaw('COALESCE(SUM(total), 0) as total_sales')
            ->selectRaw('COUNT(*) as invoices_count')
            ->selectRaw('COALESCE(SUM(commission_total), 0) as commissions_total')
            ->first();

        $avgTicket = $totals->invoices_count > 0
            ? $totals->total_sales / $totals->invoices_count
            : 0;

        $series = (clone $invoiceQuery)
            ->selectRaw($this->dateGroupExpression('issued_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(total), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $commissionSeries = (clone $invoiceQuery)
            ->selectRaw($this->dateGroupExpression('issued_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(commission_total), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $tableQuery = (clone $invoiceQuery)
            ->leftJoin('owners', 'owners.id', '=', 'invoices.owner_id')
            ->select([
                'invoices.id',
                'invoices.full_number',
                'invoices.issued_at',
                'invoices.status',
                'invoices.total',
                'invoices.paid_total',
                'owners.name as owner_name',
            ])
            ->orderByDesc('invoices.issued_at');

        $serviceBreakdown = $this->serviceBreakdown($filters);
        $productTop = $this->productTop($filters);

        $monthComparison = $this->monthComparison($filters);

        return [
            'kpis' => [
                'total_sales' => $totals->total_sales,
                'invoices_count' => $totals->invoices_count,
                'avg_ticket' => $avgTicket,
                'commissions_total' => $totals->commissions_total,
            ],
            'series' => $series,
            'commission_series' => $commissionSeries,
            'table' => $tableQuery,
            'services' => $serviceBreakdown,
            'products' => $productTop,
            'comparison' => $monthComparison,
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $rows = $this->baseInvoiceQuery($filters)
            ->leftJoin('owners', 'owners.id', '=', 'invoices.owner_id')
            ->select([
                'invoices.full_number as factura',
                'owners.name as cliente',
                'invoices.status as estado',
                'invoices.issued_at as fecha_emision',
                'invoices.total as total',
                'invoices.paid_total as pagado',
            ])
            ->orderBy('invoices.issued_at')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Factura', 'Cliente', 'Estado', 'Fecha emisión', 'Total', 'Pagado'],
            'rows' => $rows,
        ];
    }

    private function baseInvoiceQuery(ReportFilters $filters): Builder
    {
        $query = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid']);

        $this->applyDateRange($query, 'issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);
        $this->applyOptionalFilters($query, $filters, 'invoices.user_id', 'invoices.owner_id');

        return $query;
    }

    private function serviceBreakdown(ReportFilters $filters)
    {
        if (! $this->connection()->getSchemaBuilder()->hasTable('invoice_lines')) {
            return collect();
        }

        $serviceField = $this->resolveServiceField();

        if (! $serviceField) {
            return collect();
        }

        $query = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_lines.item_id')
            ->whereIn('invoices.status', ['issued', 'paid']);

        $this->applyDateRange($query, 'invoices.issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);
        $this->applyOptionalFilters($query, $filters, 'invoices.user_id', 'invoices.owner_id');

        return $query
            ->selectRaw($this->serviceTypeExpression($serviceField) . ' as service_type')
            ->selectRaw('COALESCE(SUM(invoice_lines.line_total), 0) as total')
            ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as qty')
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();
    }

    private function productTop(ReportFilters $filters)
    {
        if (! $this->connection()->getSchemaBuilder()->hasTable('invoice_lines')) {
            return collect();
        }

        $nameColumn = $this->resolveItemNameColumn();
        $query = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_lines.item_id')
            ->whereIn('invoices.status', ['issued', 'paid']);

        $productExpression = $this->isProductExpression();
        if ($productExpression) {
            $query->whereRaw($productExpression);
        }

        $this->applyDateRange($query, 'invoices.issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);
        $this->applyOptionalFilters($query, $filters, 'invoices.user_id', 'invoices.owner_id');

        return $query
            ->selectRaw("{$nameColumn} as item_name")
            ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as qty')
            ->selectRaw('COALESCE(SUM(invoice_lines.line_total), 0) as total')
            ->groupBy('item_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    private function monthComparison(ReportFilters $filters): array
    {
        $currentStart = $filters->from->copy()->startOfMonth();
        $currentEnd = $filters->from->copy()->endOfMonth();
        $previousStart = $currentStart->copy()->subMonthNoOverflow();
        $previousEnd = $previousStart->copy()->endOfMonth();

        $current = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid'])
            ->whereBetween('issued_at', [$currentStart, $currentEnd]);
        $this->applyTenant($current, 'invoices', $filters);
        $this->applyOptionalFilters($current, $filters, 'invoices.user_id', 'invoices.owner_id');

        $previous = $this->connection()->table('invoices')
            ->whereIn('status', ['issued', 'paid'])
            ->whereBetween('issued_at', [$previousStart, $previousEnd]);
        $this->applyTenant($previous, 'invoices', $filters);
        $this->applyOptionalFilters($previous, $filters, 'invoices.user_id', 'invoices.owner_id');

        $currentTotal = (float) $current->sum('total');
        $previousTotal = (float) $previous->sum('total');
        $variation = $previousTotal > 0 ? (($currentTotal - $previousTotal) / $previousTotal) * 100 : null;

        return [
            'current' => $currentTotal,
            'previous' => $previousTotal,
            'variation' => $variation,
        ];
    }
}
