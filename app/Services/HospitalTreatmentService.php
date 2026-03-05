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
    public function __construct(
        private readonly DoseSchedulerService $doseSchedulerService,
        private readonly ChargeService $chargeService,
        private readonly InventoryIntegrationService $inventoryIntegrationService,
    ) {
    }

    public function createOrder(array $data): HospitalOrder
    {
        $data = $this->normalizeSource($data);
        $this->validateSource($data);
        $data['start_at'] = $data['start_at'] ?? now();
        $data['status'] = $data['status'] ?? 'active';

        return DB::transaction(function () use ($data) {
            $order = HospitalOrder::create($data);
            $order->update(['next_due_at' => $this->doseSchedulerService->calculateFirstDueAt($order)]);

            return $order->fresh(['product']);
        });
    }

    public function stopOrder(HospitalOrder $order): HospitalOrder
    {
        $order->update(['status' => 'stopped', 'end_at' => now(), 'next_due_at' => null]);

        return $order->refresh();
    }

    public function createAdministration(HospitalOrder $order, array $payload): HospitalAdministration
    {
        return DB::transaction(function () use ($order, $payload) {
            $stay = HospitalStay::findOrFail($order->stay_id);
            $administeredAt = Carbon::parse($payload['administered_at'] ?? now());

            $this->guardAgainstDuplicateApplication($order, $administeredAt, $payload['is_admin'] ?? false);

            $day = $this->resolveDay($stay, $administeredAt);
            $payload['stay_id'] = $stay->id;
            $payload['day_id'] = $day->id;
            $payload['order_id'] = $order->id;

            $application = HospitalAdministration::create($payload);

            if ($order->source === 'inventory' && $order->product_id) {
                $this->inventoryIntegrationService->deductStock([
                    ['product_id' => $order->product_id, 'qty' => 1, 'reason' => 'hospital_application', 'ref_id' => $application->id],
                ]);
            }

            $nextDueAt = $this->doseSchedulerService->calculateNextDueAt($order, $administeredAt);
            $order->update([
                'last_applied_at' => $administeredAt,
                'next_due_at' => $nextDueAt,
                'status' => $nextDueAt ? 'active' : 'stopped',
            ]);

            $this->chargeService->createFromApplication($application->fresh(['order.product', 'stay']));

            return $application;
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

    private function guardAgainstDuplicateApplication(HospitalOrder $order, Carbon $administeredAt, bool $isAdmin): void
    {
        if ($isAdmin) {
            return;
        }

        $windowMinutes = (int) config('hospital.duplicate_window_minutes', 5);

        $exists = $order->administrations()
            ->whereBetween('administered_at', [
                $administeredAt->copy()->subMinutes($windowMinutes),
                $administeredAt->copy()->addMinutes($windowMinutes),
            ])->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'administered_at' => 'Ya existe una aplicación en una ventana cercana para esta orden.',
            ]);
        }
    }
}
