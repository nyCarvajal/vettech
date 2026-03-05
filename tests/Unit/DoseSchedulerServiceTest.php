<?php

namespace Tests\Unit;

use App\Models\HospitalOrder;
use App\Services\DoseSchedulerService;
use Carbon\Carbon;
use Tests\TestCase;

class DoseSchedulerServiceTest extends TestCase
{
    public function test_calcula_q_hours(): void
    {
        $service = new DoseSchedulerService();
        $order = new HospitalOrder(['frequency_type' => 'q_hours', 'frequency_value' => 8, 'start_at' => Carbon::parse('2026-01-01 08:00:00')]);

        $next = $service->calculateNextDueAt($order, Carbon::parse('2026-01-01 10:00:00'));

        $this->assertEquals('2026-01-01 18:00:00', $next?->format('Y-m-d H:i:s'));
    }

    public function test_calcula_times_per_day(): void
    {
        $service = new DoseSchedulerService();
        $order = new HospitalOrder(['frequency_type' => 'times_per_day', 'frequency_value' => 3, 'start_at' => Carbon::parse('2026-01-01 08:00:00')]);

        $next = $service->calculateNextDueAt($order, Carbon::parse('2026-01-01 09:00:00'));

        $this->assertEquals('2026-01-01 17:00:00', $next?->format('Y-m-d H:i:s'));
    }

    public function test_calcula_q_days(): void
    {
        $service = new DoseSchedulerService();
        $order = new HospitalOrder(['frequency_type' => 'q_days', 'frequency_value' => 2, 'start_at' => Carbon::parse('2026-01-01 08:00:00')]);

        $next = $service->calculateNextDueAt($order, Carbon::parse('2026-01-01 09:00:00'));

        $this->assertEquals('2026-01-03 09:00:00', $next?->format('Y-m-d H:i:s'));
    }
}
