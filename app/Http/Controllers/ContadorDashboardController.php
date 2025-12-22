<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardFilterRequest;
use App\Services\DashboardMetricsService;
use Carbon\Carbon;

class ContadorDashboardController extends Controller
{
    public function __construct(private DashboardMetricsService $metricsService)
    {
    }

    public function index(DashboardFilterRequest $request)
    {
        $dateFrom = $request->dateFrom();
        $dateTo = $request->dateTo();

        $metrics = $this->metricsService->getContadorMetrics($dateFrom, $dateTo);

        return view('dashboards.contador', [
            'metrics' => $metrics,
            'filter' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'preset' => $request->input('preset', 'hoy'),
            ],
        ]);
    }
}
