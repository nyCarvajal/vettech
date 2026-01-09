<?php

namespace App\Reports;

class OperationsReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters): array
    {
        $servicesQuery = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_lines.item_id')
            ->whereIn('invoices.status', ['issued', 'paid']);

        $serviceExpression = $this->isServiceExpression();
        if ($serviceExpression) {
            $servicesQuery->whereRaw($serviceExpression);
        }

        $this->applyDateRange($servicesQuery, 'invoices.issued_at', $filters);
        $this->applyTenant($servicesQuery, 'invoices', $filters);

        $series = (clone $servicesQuery)
            ->selectRaw($this->dateGroupExpression('invoices.issued_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        if ($this->connection()->getSchemaBuilder()->hasTable('users')) {
            $byUser = (clone $servicesQuery)
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->selectRaw("users.name as user_name")
                ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as total')
                ->groupBy('user_name')
                ->orderByDesc('total')
                ->get();
        } else {
            $byUser = (clone $servicesQuery)
                ->selectRaw('invoices.user_id as user_name')
                ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as total')
                ->groupBy('invoices.user_id')
                ->orderByDesc('total')
                ->get();
        }

        $tableQuery = (clone $servicesQuery)
            ->select([
                'invoices.issued_at',
                'invoice_lines.description',
                'invoice_lines.quantity',
                'invoices.user_id',
            ])
            ->orderByDesc('invoices.issued_at');

        return [
            'kpis' => [
                'services_count' => (int) (clone $servicesQuery)->sum('invoice_lines.quantity'),
            ],
            'series' => $series,
            'by_user' => $byUser,
            'table' => $tableQuery,
            'appointments' => $this->appointmentHeatmap($filters),
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $rows = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->whereBetween('invoices.issued_at', [$filters->from, $filters->to])
            ->select([
                'invoices.issued_at as fecha',
                'invoice_lines.description as servicio',
                'invoice_lines.quantity as cantidad',
            ])
            ->orderBy('invoices.issued_at')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Fecha', 'Servicio', 'Cantidad'],
            'rows' => $rows,
        ];
    }

    private function appointmentHeatmap(ReportFilters $filters): ?array
    {
        $schema = $this->connection()->getSchemaBuilder();
        $table = null;

        if ($schema->hasTable('appointments')) {
            $table = 'appointments';
        } elseif ($schema->hasTable('agenda')) {
            $table = 'agenda';
        }

        if (! $table) {
            return null;
        }

        $dateColumn = null;
        foreach (['start_at', 'scheduled_at', 'date', 'fecha'] as $candidate) {
            if ($schema->hasColumn($table, $candidate)) {
                $dateColumn = $candidate;
                break;
            }
        }

        if (! $dateColumn) {
            return null;
        }

        $query = $this->connection()->table($table)
            ->selectRaw("DAYOFWEEK({$dateColumn}) as day_of_week")
            ->selectRaw("HOUR({$dateColumn}) as hour")
            ->selectRaw('COUNT(*) as total')
            ->whereBetween($dateColumn, [$filters->from, $filters->to])
            ->groupBy('day_of_week', 'hour');

        $this->applyTenant($query, $table, $filters);

        $rows = $query->get();

        return $rows->groupBy('day_of_week')->map(function ($items) {
            return $items->pluck('total', 'hour');
        })->toArray();
    }
}
