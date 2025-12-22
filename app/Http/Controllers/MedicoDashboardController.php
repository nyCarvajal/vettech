<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicoDashboardController extends Controller
{
    public function __construct(private DashboardMetricsService $metricsService)
    {
    }

    public function index()
    {
        $metrics = $this->metricsService->getMedicoMetrics(Auth::id(), Carbon::now('America/Bogota'));

        return view('dashboards.medico', [
            'metrics' => $metrics,
        ]);
    }
}
