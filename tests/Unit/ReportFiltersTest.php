<?php

namespace Tests\Unit;

use App\Reports\ReportFilters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportFiltersTest extends TestCase
{
    public function test_it_parses_granularity_and_range(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-05-15'));

        $request = Request::create('/reports', 'GET', [
            'range' => '7d',
            'granularity' => 'week',
        ]);

        $filters = ReportFilters::fromRequest($request);

        $this->assertSame('week', $filters->granularity);
        $this->assertSame('2024-05-09', $filters->from->toDateString());
        $this->assertSame('2024-05-15', $filters->to->toDateString());
    }

    public function test_it_defaults_to_day_for_invalid_granularity(): void
    {
        $request = Request::create('/reports', 'GET', [
            'granularity' => 'invalid',
        ]);

        $filters = ReportFilters::fromRequest($request);

        $this->assertSame('day', $filters->granularity);
    }
}
