<?php

namespace App\Services;

use App\Models\HospitalOrder;
use Carbon\Carbon;

class DoseSchedulerService
{
    public function calculateFirstDueAt(HospitalOrder $order, ?Carbon $reference = null): ?Carbon
    {
        $reference ??= now();

        $startAt = $order->start_at?->copy() ?? $reference->copy();

        if ($order->frequency_type === 'times_per_day') {
            $anchor = $this->resolveTimesPerDayAnchor($startAt);
            $dueAt = $anchor->copy()->addHours($this->intervalHours($order));
        } else {
            $dueAt = $startAt->copy()->addSeconds($this->intervalSeconds($order));
        }

        return $this->rollForward($order, $dueAt, $reference);
    }

    public function calculateNextDueAt(HospitalOrder $order, Carbon $appliedAt): ?Carbon
    {
        if ($this->isFinished($order, $appliedAt)) {
            return null;
        }

        $candidate = $appliedAt->copy()->addSeconds($this->intervalSeconds($order));

        return $this->rollForward($order, $candidate, now());
    }

    public function isFinished(HospitalOrder $order, Carbon $date): bool
    {
        if ($order->end_at && $date->greaterThan($order->end_at)) {
            return true;
        }

        if ($order->duration_days) {
            $start = $order->start_at?->copy() ?? Carbon::parse($order->created_at);
            $maxDate = $start->copy()->addDays((int) $order->duration_days);

            return $date->greaterThan($maxDate);
        }

        return false;
    }

    private function intervalSeconds(HospitalOrder $order): int
    {
        $value = max(1, (int) ($order->frequency_value ?? 1));

        return match ($order->frequency_type) {
            'q_days' => $value * 24 * 3600,
            'times_per_day' => (int) round((24 / $value) * 3600),
            default => $value * 3600,
        };
    }

    private function intervalHours(HospitalOrder $order): int
    {
        $value = max(1, (int) ($order->frequency_value ?? 1));

        return (int) round(24 / $value);
    }

    private function resolveTimesPerDayAnchor(Carbon $startAt): Carbon
    {
        if ($startAt->copy()->startOfHour()->equalTo($startAt)) {
            return $startAt;
        }

        return now()->copy()->addHour()->startOfHour();
    }

    private function rollForward(HospitalOrder $order, Carbon $candidate, Carbon $reference): ?Carbon
    {
        $intervalSeconds = $this->intervalSeconds($order);

        while ($candidate->lessThanOrEqualTo($reference)) {
            $candidate->addSeconds($intervalSeconds);
            if ($this->isFinished($order, $candidate)) {
                return null;
            }
        }

        if ($this->isFinished($order, $candidate)) {
            return null;
        }

        return $candidate;
    }
}
