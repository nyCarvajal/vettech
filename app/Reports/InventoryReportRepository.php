<?php

namespace App\Reports;

class InventoryReportRepository extends ReportRepository
{
    public function summary(ReportFilters $filters, ?int $itemId = null): array
    {
        $movementsQuery = $this->connection()->table('inventory_movements')
            ->leftJoin('items', 'items.id', '=', 'inventory_movements.item_id');

        $this->applyDateRange($movementsQuery, 'occurred_at', $filters);
        $this->applyTenant($movementsQuery, 'inventory_movements', $filters);

        if ($itemId) {
            $movementsQuery->where('inventory_movements.item_id', $itemId);
        }

        $series = (clone $movementsQuery)
            ->selectRaw($this->dateGroupExpression('occurred_at', $filters->granularity) . ' as label')
            ->selectRaw('COALESCE(SUM(quantity), 0) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $tableQuery = (clone $movementsQuery)
            ->select([
                'inventory_movements.occurred_at',
                'inventory_movements.movement_type',
                'inventory_movements.quantity',
                'inventory_movements.unit_cost',
                $this->resolveItemNameColumn() . ' as item_name',
            ])
            ->orderByDesc('inventory_movements.occurred_at');

        $lowStock = $this->lowStockItems($filters);
        $rotation = $this->rotationTop($filters);
        $valuation = $this->inventoryValuation($filters);

        return [
            'kpis' => [
                'movements_count' => (int) (clone $movementsQuery)->count(),
            ],
            'series' => $series,
            'table' => $tableQuery,
            'low_stock' => $lowStock,
            'rotation' => $rotation,
            'valuation' => $valuation,
        ];
    }

    public function exportData(ReportFilters $filters): array
    {
        $query = $this->connection()->table('inventory_movements')
            ->leftJoin('items', 'items.id', '=', 'inventory_movements.item_id')
            ->whereBetween('occurred_at', [$filters->from, $filters->to])
            ->select([
                'inventory_movements.occurred_at as fecha',
                'inventory_movements.movement_type as tipo',
                'inventory_movements.quantity as cantidad',
                $this->resolveItemNameColumn() . ' as item',
            ])
            ->orderBy('inventory_movements.occurred_at');

        $this->applyTenant($query, 'inventory_movements', $filters);

        $rows = $query->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return [
            'headers' => ['Fecha', 'Tipo', 'Cantidad', 'Item'],
            'rows' => $rows,
        ];
    }

    private function lowStockItems(ReportFilters $filters)
    {
        $minColumn = $this->tableHasColumn('items', 'cantidad') ? 'cantidad' : null;
        $stockColumn = $this->tableHasColumn('items', 'stock') ? 'stock' : null;
        $hasTrack = $this->tableHasColumn('items', 'track_inventory');

        if (! $minColumn || ! $stockColumn || ! $hasTrack) {
            return collect();
        }

        $query = $this->connection()->table('items')
            ->where('track_inventory', true)
            ->whereColumn($stockColumn, '<=', $minColumn)
            ->select([
                $this->resolveItemNameColumn() . ' as item_name',
                $stockColumn . ' as stock',
                $minColumn . ' as minimum',
            ])
            ->orderBy('stock');

        $this->applyTenant($query, 'items', $filters);

        return $query->limit(15)->get();
    }

    private function rotationTop(ReportFilters $filters)
    {
        $query = $this->connection()->table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->leftJoin('items', 'items.id', '=', 'invoice_lines.item_id')
            ->whereIn('invoices.status', ['issued', 'paid']);

        $this->applyDateRange($query, 'invoices.issued_at', $filters);
        $this->applyTenant($query, 'invoices', $filters);

        return $query
            ->selectRaw($this->resolveItemNameColumn() . ' as item_name')
            ->selectRaw('COALESCE(SUM(invoice_lines.quantity), 0) as qty')
            ->groupBy('item_name')
            ->orderByDesc('qty')
            ->limit(10)
            ->get();
    }

    private function inventoryValuation(ReportFilters $filters): ?float
    {
        $costColumn = null;
        if ($this->tableHasColumn('items', 'cost_price')) {
            $costColumn = 'cost_price';
        } elseif ($this->tableHasColumn('items', 'costo')) {
            $costColumn = 'costo';
        }

        if (! $costColumn) {
            return null;
        }

        $query = $this->connection()->table('items')
            ->selectRaw(\"COALESCE(SUM(stock * {$costColumn}), 0) as total\");

        $this->applyTenant($query, 'items', $filters);

        return (float) $query->value('total');
    }
}
