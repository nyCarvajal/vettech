<?php

namespace App\Reports;

class GroomingReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters): array
    {
        if (! $this->connection()->getSchemaBuilder()->hasTable('invoice_lines')) {
            return [
                'kpis' => [
                    'grooming_total' => 0,
                    'services_count' => 0,
                ],
                'series' => collect(),
                'top_services' => collect(),
                'top_clients' => collect(),
                'table' => $this->connection()->table('invoice_lines')->whereRaw('1=0'),
            ];
        }

        $query = $this->groomingLinesQuery($filters);

        $series = (clone $query)
            ->selectRaw($this->dateGroupExpression('invoices.issued_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(invoice_lines.line_total), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $topServices = (clone $query)
            ->selectRaw('invoice_lines.description as service')
            ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as qty')
            ->selectRaw('COALESCE(SUM(invoice_lines.line_total), 0) as total')
            ->groupBy('service')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topClients = (clone $query)
            ->leftJoin('owners', 'owners.id', '=', 'invoices.owner_id')
            ->selectRaw('owners.name as owner_name')
            ->selectRaw('COUNT(*) as total_services')
            ->groupBy('owners.name')
            ->orderByDesc('total_services')
            ->limit(10)
            ->get();

        $tableQuery = (clone $query)
            ->select([
                'invoices.issued_at',
                'invoice_lines.description',
                'invoice_lines.quantity',
                'invoice_lines.line_total',
            ])
            ->orderByDesc('invoices.issued_at');

        return [
            'kpis' => [
                'grooming_total' => (float) (clone $query)->sum('invoice_lines.line_total'),
                'services_count' => (int) (clone $query)->sum('invoice_lines.quantity'),
            ],
            'series' => $series,
            'top_services' => $topServices,
            'top_clients' => $topClients,
            'table' => $tableQuery,
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $rows = $this->groomingLinesQuery($filters)
            ->select([
                'invoices.issued_at as fecha',
                'invoice_lines.description as servicio',
                'invoice_lines.quantity as cantidad',
                'invoice_lines.line_total as total',
            ])
            ->orderBy('invoices.issued_at')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Fecha', 'Servicio', 'Cantidad', 'Total'],
            'rows' => $rows,
        ];
    }

    private function groomingLinesQuery(ReportFilters $filters)
    {
        $query = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_lines.item_id')
            ->whereIn('invoices.status', ['issued', 'paid']);

        $areaValues = config('reporting.grooming_area_values', []);
        if ($this->tableHasColumn('items', 'area') && $areaValues) {
            $query->whereIn('items.area', $areaValues);
        } elseif ($this->tableHasColumn('items', 'tipo') && $areaValues) {
            $query->whereIn('items.tipo', $areaValues);
        }

        $this->applyDateRange($query, 'invoices.issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);

        return $query;
    }
}
