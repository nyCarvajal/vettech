<?php

namespace App\Reports;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportFilters
{
    public Carbon $from;
    public Carbon $to;
    public string $granularity;
    public ?int $userId;
    public ?int $ownerId;
    public ?string $paymentMethod;
    public ?int $tenantId;

    public function __construct(
        Carbon $from,
        Carbon $to,
        string $granularity = 'day',
        ?int $userId = null,
        ?int $ownerId = null,
        ?string $paymentMethod = null,
        ?int $tenantId = null,
    ) {
        $this->from = $from;
        $this->to = $to;
        $this->granularity = $granularity;
        $this->userId = $userId;
        $this->ownerId = $ownerId;
        $this->paymentMethod = $paymentMethod;
        $this->tenantId = $tenantId;
    }

    public static function fromRequest(Request $request): self
    {
        $today = Carbon::today();
        $range = $request->string('range')->toString();
        $from = match ($range) {
            'today' => $today->copy(),
            '7d' => $today->copy()->subDays(6),
            '30d' => $today->copy()->subDays(29),
            'this_month' => $today->copy()->startOfMonth(),
            'last_month' => $today->copy()->subMonthNoOverflow()->startOfMonth(),
            default => null,
        };

        $to = match ($range) {
            'last_month' => $today->copy()->subMonthNoOverflow()->endOfMonth(),
            default => null,
        };

        $fromInput = $request->date('from');
        $toInput = $request->date('to');

        $from = $fromInput ?? $from ?? $today->copy()->subDays(29);
        $to = $toInput ?? $to ?? $today->copy()->endOfDay();

        $granularity = $request->string('granularity', 'day')->toString();
        if (! in_array($granularity, ['day', 'week', 'month'], true)) {
            $granularity = 'day';
        }

        return new self(
            $from->startOfDay(),
            $to->endOfDay(),
            $granularity,
            $request->integer('user_id') ?: null,
            $request->integer('owner_id') ?: null,
            $request->string('payment_method')->toString() ?: null,
            $request->user()?->tenant_id ?? null,
        );
    }

    public function rangeLabel(): string
    {
        return $this->from->format('Y-m-d') . ' → ' . $this->to->format('Y-m-d');
    }
}
