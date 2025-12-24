<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\HospitalStay;
use App\Models\HospitalTask;
use App\Models\Paciente;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\Reserva;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardMetricsService
{
    private string $timezone = 'America/Bogota';

    public function getMedicoMetrics(int $userId, $date): array
    {
        $now = Carbon::parse($date, $this->timezone)->setTimezone($this->timezone);
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();

        $appointments = Reserva::with(['paciente', 'tipocita'])
            ->where('entrenador_id', $userId)
            ->whereBetween('fecha', [$startOfDay, $endOfDay])
            ->orderBy('fecha')
            ->limit(8)
            ->get();

        $pendingTasks = HospitalTask::with(['stay.cage'])
            ->whereHas('stay', fn ($q) => $q->whereNull('discharged_at'))
            ->where(function ($q) use ($startOfDay, $endOfDay) {
                $q->whereNull('end_at')
                    ->orWhereBetween('end_at', [$startOfDay, $endOfDay]);
            })
            ->orderBy('end_at')
            ->limit(10)
            ->get();

        $hospitalStays = HospitalStay::whereNull('discharged_at')
            ->selectRaw('coalesce(severity, "desconocido") as severity, count(*) as total')
            ->groupBy('severity')
            ->get();

        $recentConsultations = Reserva::with(['paciente'])
            ->where('entrenador_id', $userId)
            ->whereNotNull('fecha')
            ->orderByDesc('fecha')
            ->limit(5)
            ->get();

        $pendingPrescriptions = Prescription::with('professional')
            ->where('professional_id', $userId)
            ->where(function ($q) {
                $q->where('status', 'signed')
                    ->orWhereNull('status');
            })
            ->whereDoesntHave('dispensations')
            ->limit(10)
            ->get();

        $patientsWithOverdueControls = collect();
        if (Schema::hasColumn('pacientes', 'proximo_control_at')) {
            $patientsWithOverdueControls = Paciente::query()
                ->whereNotNull('proximo_control_at')
                ->where('proximo_control_at', '<', $startOfDay)
                ->orderBy('proximo_control_at')
                ->limit(10)
                ->get();
        }

        return [
            'appointments' => $appointments,
            'pendingTasks' => $pendingTasks,
            'pendingTaskCount' => $pendingTasks->count(),
            'hospitalSummary' => $hospitalStays,
            'recentConsultations' => $recentConsultations,
            'pendingPrescriptions' => $pendingPrescriptions,
            'overdueControls' => $patientsWithOverdueControls,
        ];
    }

    public function getAdminMetrics($date): array
    {
        $now = Carbon::parse($date, $this->timezone);
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $monthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $monthStart->copy()->subMonth();
        $previousMonthEnd = $monthStart->copy()->subDay();

        $todaySales = $this->cachedAggregate('admin.sales.today', function () use ($todayStart, $todayEnd) {
            return Sale::where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'void');
            })
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->selectRaw('"desconocido" as method, sum(total) as total')
                ->groupBy('method')
                ->get();
        });

        $monthSales = $this->cachedAggregate('admin.sales.month', function () use ($monthStart, $todayEnd) {
            return Sale::where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'void');
            })
                ->whereBetween('created_at', [$monthStart, $todayEnd])
                ->selectRaw('"desconocido" as method, sum(total) as total')
                ->groupBy('method')
                ->get();
        });

        $previousMonthSales = $this->cachedAggregate('admin.sales.prev_month', function () use ($previousMonthStart, $previousMonthEnd) {
            return Sale::where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'void');
            })
                ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
                ->sum('total');
        });

        $cashSessions = CashSession::whereBetween('opened_at', [$todayStart, $todayEnd])
            ->get();

        $hospitalOccupancy = HospitalStay::selectRaw('count(*) as total, sum(case when discharged_at is null then 1 else 0 end) as active')
            ->first();

        $hospitalRevenueToday = Sale::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'void');
        })
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('total');

        $hospitalRevenueMonth = Sale::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'void');
        })
            ->whereBetween('created_at', [$monthStart, $todayEnd])
            ->sum('total');

        $lowStock = Product::withSum('batches as stock_available', 'qty_available')
            ->orderBy('stock_available')
            ->limit(10)
            ->get();

        $expiringBatches = Batch::with('product')
            ->whereDate('expires_at', '<=', $todayStart->copy()->addDays(30))
            ->orderBy('expires_at')
            ->limit(10)
            ->get();

        $appointmentsToday = Reserva::whereBetween('fecha', [$todayStart, $todayEnd])->count();
        $noShows = Reserva::whereBetween('fecha', [$todayStart, $todayEnd])
            ->where('estado', 'No Asistida')
            ->count();

        return [
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'previousMonthSales' => $previousMonthSales,
            'cashSessions' => $cashSessions,
            'hospitalOccupancy' => $hospitalOccupancy,
            'hospitalRevenueToday' => $hospitalRevenueToday,
            'hospitalRevenueMonth' => $hospitalRevenueMonth,
            'lowStock' => $lowStock,
            'expiringBatches' => $expiringBatches,
            'appointmentsToday' => $appointmentsToday,
            'noShows' => $noShows,
        ];
    }

    public function getContadorMetrics(Carbon $dateFrom, Carbon $dateTo): array
    {
        $dateFrom = $dateFrom->copy()->setTimezone($this->timezone)->startOfDay();
        $dateTo = $dateTo->copy()->setTimezone($this->timezone)->endOfDay();

        $sales = Sale::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'void');
        })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('"desconocido" as method, sum(total) as total')
            ->groupBy('method')
            ->get();

        $cashSessions = CashSession::with('register')
            ->whereBetween('opened_at', [$dateFrom, $dateTo])
            ->orderBy('opened_at')
            ->get();

        $expenses = CashMovement::where('type', 'expense')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $voidSales = Sale::where('status', 'void')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'sales' => $sales,
            'cashSessions' => $cashSessions,
            'expenses' => $expenses,
            'voidSales' => $voidSales,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    private function cachedAggregate(string $key, callable $callback): Collection|int
    {
        return Cache::remember($key, 60, $callback);
    }
}
