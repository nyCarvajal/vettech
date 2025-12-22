<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class DashboardFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'preset' => ['nullable', 'in:hoy,7d,30d'],
        ];
    }

    public function dateFrom(): Carbon
    {
        $preset = $this->input('preset', 'hoy');
        $now = Carbon::now('America/Bogota');

        return match ($preset) {
            '7d' => $now->copy()->subDays(6),
            '30d' => $now->copy()->subDays(29),
            default => Carbon::parse($this->input('date_from', $now->toDateString()), 'America/Bogota'),
        };
    }

    public function dateTo(): Carbon
    {
        $preset = $this->input('preset', 'hoy');
        $now = Carbon::now('America/Bogota');

        return match ($preset) {
            '7d', '30d' => $now,
            default => Carbon::parse($this->input('date_to', $now->toDateString()), 'America/Bogota'),
        };
    }
}
