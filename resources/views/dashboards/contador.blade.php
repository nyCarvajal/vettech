@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard contador</h2>
            <small class="text-muted">Control financiero y cierres</small>
        </div>
        <form class="d-flex gap-2" method="GET">
            <select name="preset" class="form-select form-select-sm" style="width:auto">
                <option value="hoy" @selected(($filter['preset'] ?? '') === 'hoy')>Hoy</option>
                <option value="7d" @selected(($filter['preset'] ?? '') === '7d')>Últimos 7 días</option>
                <option value="30d" @selected(($filter['preset'] ?? '') === '30d')>Últimos 30 días</option>
            </select>
            <input type="date" name="date_from" value="{{ $filter['date_from'] }}" class="form-control form-control-sm" />
            <input type="date" name="date_to" value="{{ $filter['date_to'] }}" class="form-control form-control-sm" />
            <button class="btn btn-primary btn-sm">Aplicar</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            @include('dashboards._kpi_card', [
                'title' => 'Ventas pagadas',
                'value' => '$' . number_format($metrics['sales']->sum('total'), 0, ',', '.'),
                'subtitle' => $metrics['sales']->map(fn ($sale) => ucfirst($sale->method) . ': $' . number_format($sale->total, 0, ',', '.'))->implode(' | '),
            ])
        </div>
        <div class="col-md-4">
            @include('dashboards._kpi_card', [
                'title' => 'Egresos del periodo',
                'value' => '$' . number_format($metrics['expenses']->sum('amount'), 0, ',', '.'),
                'subtitle' => 'Top 10 mostrados abajo',
            ])
        </div>
        <div class="col-md-4">
            @include('dashboards._kpi_card', [
                'title' => 'Ventas anuladas',
                'value' => $metrics['voidSales']->count(),
                'subtitle' => 'Listado de las últimas 10',
            ])
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Caja por turno ({{ $filter['date_from'] }} - {{ $filter['date_to'] }})</div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['cashSessions'] as $session)
                        @php
                            $difference = ($session->closing_amount_counted ?? 0) - ($session->closing_amount_expected ?? 0);
                            $status = $difference === 0 ? 'OK' : 'Diferencia';
                        @endphp
                        @include('dashboards._list_item', [
                            'title' => optional($session->opened_at)->format('H:i') . ' - ' . optional($session->closed_at)->format('H:i'),
                            'subtitle' => 'Esperado: $' . number_format($session->closing_amount_expected ?? 0, 0, ',', '.') . ' · Contado: $' . number_format($session->closing_amount_counted ?? 0, 0, ',', '.') . ' · Dif: $' . number_format($difference, 0, ',', '.'),
                            'meta' => $status,
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin sesiones en el rango</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Egresos del día</div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['expenses'] as $expense)
                        @include('dashboards._list_item', [
                            'title' => '$' . number_format($expense->amount, 0, ',', '.'),
                            'subtitle' => $expense->reason ?? 'Movimiento',
                            'meta' => optional($expense->created_at)->format('d/m H:i'),
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin egresos en el rango</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Ventas anuladas</div>
                <ul class="list-group list-group-flush">
                    @forelse ($metrics['voidSales'] as $sale)
                        @include('dashboards._list_item', [
                            'title' => 'Venta #' . $sale->id,
                            'subtitle' => 'Total: $' . number_format($sale->total ?? 0, 0, ',', '.') . ' · ' . optional($sale->created_at)->format('d/m H:i'),
                        ])
                    @empty
                        <li class="list-group-item text-muted">Sin anulaciones</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Exportables</span>
                    <div class="btn-group btn-group-sm">
                        <a href="#" class="btn btn-outline-primary">Exportar Excel</a>
                        <a href="#" class="btn btn-outline-secondary">Exportar CSV</a>
                    </div>
                </div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <a href="{{ route('cajas.index') }}" class="btn btn-primary btn-sm">Revisar cierres pendientes</a>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-primary btn-sm">Ver ventas por rango</a>
                    <a href="{{ route('salidas.index') }}" class="btn btn-outline-secondary btn-sm">Exportar movimientos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
