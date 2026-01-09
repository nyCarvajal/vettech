<?php

namespace App\Reports;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class ReportRepository
{
    protected function connection(): Connection
    {
        return DB::connection($this->connectionName());
    }

    protected function connectionName(): string
    {
        $tenantDatabase = config('database.connections.tenant.database');

        if ($tenantDatabase) {
            return 'tenant';
        }

        return DB::getDefaultConnection();
    }

    protected function applyDateRange(Builder $query, string $column, ReportFilters $filters): void
    {
        $query->whereBetween($column, [$filters->from, $filters->to]);
    }

    protected function applyTenant(Builder $query, string $table, ReportFilters $filters): void
    {
        if ($filters->tenantId && $this->tableHasColumn($table, 'tenant_id')) {
            $query->where("{$table}.tenant_id", $filters->tenantId);
        }
    }

    protected function applyOptionalFilters(Builder $query, ReportFilters $filters, ?string $userColumn = null, ?string $ownerColumn = null): void
    {
        if ($filters->userId && $userColumn) {
            $query->where($userColumn, $filters->userId);
        }

        if ($filters->ownerId && $ownerColumn) {
            $query->where($ownerColumn, $filters->ownerId);
        }
    }

    protected function dateGroupExpression(string $column, string $granularity): string
    {
        $driver = $this->connection()->getDriverName();

        if ($driver === 'sqlite') {
            return match ($granularity) {
                'week' => "strftime('%Y-%W', {$column})",
                'month' => "strftime('%Y-%m', {$column})",
                default => "date({$column})",
            };
        }

        return match ($granularity) {
            'week' => "DATE_FORMAT({$column}, '%x-%v')",
            'month' => "DATE_FORMAT({$column}, '%Y-%m')",
            default => "DATE({$column})",
        };
    }

    protected function tableHasColumn(string $table, string $column): bool
    {
        return Schema::connection($this->connectionName())->hasColumn($table, $column);
    }

    protected function resolveItemNameColumn(): string
    {
        if ($this->tableHasColumn('items', 'nombres')) {
            return 'items.nombres';
        }

        if ($this->tableHasColumn('items', 'name')) {
            return 'items.name';
        }

        return 'items.nombre';
    }

    protected function resolveServiceField(): ?string
    {
        if ($this->tableHasColumn('invoice_lines', 'service_type')) {
            return 'invoice_lines.service_type';
        }

        if ($this->tableHasColumn('items', 'service_type')) {
            return 'items.service_type';
        }

        if ($this->tableHasColumn('items', 'area')) {
            return 'items.area';
        }

        if ($this->tableHasColumn('items', 'tipo')) {
            return 'items.tipo';
        }

        return null;
    }

    protected function serviceTypeExpression(?string $field): string
    {
        if (! $field) {
            return "'No disponible'";
        }

        $map = config('reporting.service_type_map', []);
        $cases = [];
        foreach ($map as $key => $label) {
            $cases[] = "WHEN {$field} = '{$key}' THEN '{$label}'";
        }

        if (! $cases) {
            return $field;
        }

        $casesSql = implode(' ', $cases);

        return "CASE {$casesSql} ELSE {$field} END";
    }

    protected function isProductExpression(): ?string
    {
        if ($this->tableHasColumn('items', 'type')) {
            return "items.type = 'product'";
        }

        return null;
    }

    protected function isServiceExpression(): ?string
    {
        if ($this->tableHasColumn('items', 'type')) {
            return "items.type = 'service'";
        }

        return null;
    }
}
