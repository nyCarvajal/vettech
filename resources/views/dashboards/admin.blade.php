@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard administrador</h2>
            <small class="text-muted">Visión rápida del negocio y operación</small>
        </div>
        <div class="btn-group">
            <a href="{{ route('users.admins.create') }}" class="btn btn-primary">Crear usuario / rol</a>
            <a href="{{ route('items.create') }}" class="btn btn-outline-secondary">Crear producto / lote</a>
            <a href="{{ route('kardex.index') }}" class="btn btn-outline-secondary">Ver reportes</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            @include('dashboards._kpi_card', [
                'title' => 'Ventas HOY',
                'value' => '$' . number_format($metrics['todaySales']->sum('total'), 0, ',', '.'),
                'subtitle' => $metrics['todaySales']->map(fn ($sale) => ucfirst($sale->method) . ': $' . number_format($sale->total, 0, ',', '.'))->implode(' | '),
            ])
        </div>
        <div class="col-md-3">
            @php
                $monthTotal = $metrics['monthSales']->sum('total');
                $growth = $metrics['previousMonthSales'] > 0
                    ? round((($monthTotal - $metrics['previousMonthSales']) / max($metrics['previousMonthSales'], 1)) * 100)
                    : 100;
            @endphp
            @include('dashboards._kpi_card', [
                'title' => 'Ventas MES',
                'value' => '$' . number_format($monthTotal, 0, ',', '.'),
                'subtitle' => 'Vs mes anterior: ' . $growth . '%',
            ])
        </div>
        <div class="col-md-3">
            @include('dashboards._kpi_card', [
                'title' => 'Sesiones de caja',
                'value' => $metrics['cashSessions']->whereNull('closed_at')->count() . ' abiertas / ' . $metrics['cashSessions']->count(),
                'subtitle' => 'Alertas: ' . $metrics['cashSessions']->whereNull('closed_at')->count() . ' sin cierre',
            ])
        </div>
        <div class="col-md-3">
            @include('dashboards._kpi_card', [
                'title' => 'Ocupación hospital',
                'value' => ($metrics['hospitalOccupancy']->active ?? 0) . ' / ' . ($metrics['hospitalOccupancy']->total ?? 0),
                'subtitle' => 'Ingresos HOY: $' . number_format($metrics['hospitalRevenueToday'], 0, ',', '.') . ' | Mes: $' . number_format($metrics['hospitalRevenueMonth'], 0, ',', '.'),
            ])
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Inventario bajo stock (top 10)</span>
                    <small class="text-muted">Prioridad</small>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['lowStock'] as $product)
                        @include('dashboards._list_item', [
                            'title' => $product->name,
                            'subtitle' => 'Stock: ' . ($product->stock_available ?? 0) . ' / Min: ' . ($product->min_stock ?? 0),
                            'meta' => 'SKU ' . ($product->sku ?? '-')
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin alertas de stock</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Lotes por vencer (30 días)</span>
                    <small class="text-muted">Top 10</small>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['expiringBatches'] as $batch)
                        @include('dashboards._list_item', [
                            'title' => $batch->product->name ?? 'Producto',
                            'subtitle' => 'Lote ' . ($batch->batch_code ?? '-') . ' - vence ' . optional($batch->expires_at)->format('d/m'),
                            'meta' => $batch->qty_available . ' uds'
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin lotes próximos a vencer</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Citas de hoy</span>
                    <span class="badge bg-light text-dark">{{ $metrics['appointmentsToday'] }} agendadas</span>
                </div>
                <div class="card-body">
                    <p class="mb-1">No asistencias: {{ $metrics['noShows'] }}</p>
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('reservas.index') }}">Ver agenda completa</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header">Accesos rápidos</div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <a href="{{ route('users.admins.create') }}" class="btn btn-primary">Crear usuario</a>
                    <a href="{{ route('items.create') }}" class="btn btn-outline-primary">Crear producto</a>
                    <a href="{{ route('kardex.index') }}" class="btn btn-outline-secondary">Ver reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
