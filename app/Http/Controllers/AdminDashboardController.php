<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct(private DashboardMetricsService $metricsService)
    {
    }

    public function index()
    {
        $metrics = $this->metricsService->getAdminMetrics(Carbon::now('America/Bogota'));

        return view('dashboards.admin', [
            'metrics' => $metrics,
        ]);
    }
}
