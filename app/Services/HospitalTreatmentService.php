<?php

namespace App\Services;

use App\Models\HospitalAdministration;
use App\Models\HospitalDay;
use App\Models\HospitalOrder;
use App\Models\HospitalStay;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HospitalTreatmentService
{
    public function createOrder(array $data): HospitalOrder
    {
        $data = $this->normalizeSource($data);
        $this->validateSource($data);

        return DB::transaction(function () use ($data) {
            $order = HospitalOrder::create($data);

            return $order->fresh(['product']);
        });
    }

    public function stopOrder(HospitalOrder $order): HospitalOrder
    {
        $order->update(['status' => 'stopped', 'end_at' => now()]);

        return $order->refresh();
    }

    public function scheduleFromFrequency(string $frequency): array
    {
        return match (strtolower($frequency)) {
            'bid', 'c/12h' => ['08:00', '20:00'],
            'tid', 'c/8h' => ['07:00', '15:00', '23:00'],
            'qid', 'c/6h' => ['06:00', '12:00', '18:00', '00:00'],
            default => [],
        };
    }

    public function createAdministration(HospitalOrder $order, array $payload): HospitalAdministration
    {
        return DB::transaction(function () use ($order, $payload) {
            $stay = HospitalStay::findOrFail($order->stay_id);
            $administeredAt = Carbon::parse($payload['administered_at'] ?? now());
            $day = $this->resolveDay($stay, $administeredAt);

            $payload['stay_id'] = $stay->id;
            $payload['day_id'] = $day->id;
            $payload['order_id'] = $order->id;

            return HospitalAdministration::create($payload);
        });
    }

    protected function resolveDay(HospitalStay $stay, Carbon $date): HospitalDay
    {
        $dayNumber = $stay->days()->count() + 1;

        return $stay->days()->firstOrCreate(
            ['date' => $date->toDateString()],
            ['day_number' => $dayNumber]
        );
    }

    protected function normalizeSource(array $data): array
    {
        if (($data['source'] ?? null) === 'inventory' && empty($data['product_id']) && ! empty($data['manual_name'])) {
            $data['source'] = 'manual';
        }

        return $data;
    }

    protected function validateSource(array $data): void
    {
        if ($data['source'] === 'inventory' && empty($data['product_id'])) {
            throw ValidationException::withMessages([
                'product_id' => 'El producto es requerido para órdenes desde inventario.',
            ]);
        }

        if ($data['source'] === 'manual' && empty($data['manual_name'])) {
            throw ValidationException::withMessages([
                'manual_name' => 'El nombre es requerido para órdenes manuales.',
            ]);
        }
    }
}
