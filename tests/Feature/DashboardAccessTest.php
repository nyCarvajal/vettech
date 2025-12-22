<?php

namespace Tests\Feature;

use App\Http\Middleware\ConnectTenantDB;
use App\Models\User;
use App\Services\DashboardMetricsService;
use Mockery;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    public function test_dashboard_redirects_by_role()
    {
        $user = new User();
        $user->id = 1;
        $user->role = 'admin';

        $this->withoutMiddleware([ConnectTenantDB::class]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('dashboard.admin'));
    }

    public function test_forbidden_for_wrong_dashboard()
    {
        $user = new User();
        $user->id = 2;
        $user->role = 'medico';

        $this->withoutMiddleware([ConnectTenantDB::class]);

        $response = $this->actingAs($user)->get('/dashboard/admin');

        $response->assertStatus(403);
    }

    public function test_admin_dashboard_loads_metrics()
    {
        $user = new User();
        $user->id = 3;
        $user->role = 'admin';

        $this->withoutMiddleware([ConnectTenantDB::class]);

        $mockService = Mockery::mock(DashboardMetricsService::class);
        $mockService->shouldReceive('getAdminMetrics')->once()->andReturn([
            'todaySales' => collect(),
            'monthSales' => collect(),
            'previousMonthSales' => 0,
            'cashSessions' => collect(),
            'hospitalOccupancy' => (object) ['total' => 0, 'active' => 0],
            'hospitalRevenueToday' => 0,
            'hospitalRevenueMonth' => 0,
            'lowStock' => collect(),
            'expiringBatches' => collect(),
            'appointmentsToday' => 0,
            'noShows' => 0,
        ]);

        $this->app->instance(DashboardMetricsService::class, $mockService);

        $response = $this->actingAs($user)->get('/dashboard/admin');

        $response->assertOk();
    }
}
