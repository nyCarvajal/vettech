<?php

namespace App\Services;

use App\Models\HospitalDay;
use App\Models\HospitalStay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HospitalStayService
{
    public function admit(array $payload): HospitalStay
    {
        return DB::transaction(function () use ($payload) {
            $stay = HospitalStay::create($payload);

            $this->createDayIfMissing($stay, Carbon::parse($stay->admitted_at));

            return $stay->fresh(['days']);
        });
    }

    public function discharge(HospitalStay $stay, ?Carbon $dischargedAt = null): HospitalStay
    {
        $stay->update([
            'status' => 'discharged',
            'discharged_at' => $dischargedAt ?? now(),
        ]);

        return $stay->refresh();
    }

    public function ensureDays(HospitalStay $stay): void
    {
        $start = Carbon::parse($stay->admitted_at)->startOfDay();
        $today = now()->startOfDay();

        $day = 1;
        for ($date = $start; $date->lte($today); $date->addDay()) {
            $this->createDayIfMissing($stay, $date, $day);
            $day++;
        }
    }

    protected function createDayIfMissing(HospitalStay $stay, Carbon $date, ?int $dayNumber = null): HospitalDay
    {
        $existing = $stay->days()->whereDate('date', $date->toDateString())->first();
        if ($existing) {
            return $existing;
        }

        $dayNumber ??= $stay->days()->max('day_number') + 1 ?? 1;

        return $stay->days()->create([
            'date' => $date->toDateString(),
            'day_number' => $dayNumber,
        ]);
    }
}
