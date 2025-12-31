<?php

namespace App\Services;

use App\Models\HospitalConsumption;
use App\Models\HospitalStay;
use App\Models\HospitalTask;
use App\Models\HospitalTaskLog;
use App\Models\Product;
use App\Models\ShiftInstance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HospitalService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function admit(array $data, User $user): HospitalStay
    {
        $data['created_by'] = $user->id;
        return HospitalStay::create($data);
    }

    public function discharge(HospitalStay $stay, User $user): HospitalStay
    {
        if ($stay->status === 'discharged') {
            return $stay;
        }
        $stay->update([
            'status' => 'discharged',
            'discharged_at' => now(),
        ]);
        return $stay;
    }

    public function logTask(HospitalTask $task, ShiftInstance $shift, User $user, string $status, ?string $notes = null): HospitalTaskLog
    {
        if ($task->stay->status === 'discharged') {
            throw new InvalidArgumentException('No se pueden registrar tareas en un alta.');
        }

        return HospitalTaskLog::create([
            'task_id' => $task->id,
            'shift_instance_id' => $shift->id,
            'performed_by' => $user->id,
            'performed_at' => now(),
            'status' => $status,
            'notes' => $notes,
        ]);
    }

    public function consumeFromTask(HospitalStay $stay, Product $product, int $qty, User $user, ?int $batchId = null): HospitalConsumption
    {
        if ($stay->status === 'discharged') {
            throw new InvalidArgumentException('No se pueden registrar consumos en un alta.');
        }

        $batch = null;
        if ($product->requires_batch) {
            $batch = $batchId ? $product->batches()->findOrFail($batchId) : $this->inventoryService->fefoPickBatch($product);
            if (!$batch) {
                throw new InvalidArgumentException('No hay lote disponible.');
            }
        }

        $this->inventoryService->ensureNonNegative($product, $qty);
        $this->inventoryService->moveStock($product, $batch, 'out', $qty, 'consumo hospital', 'hospital_stay', $stay->id, $user);

        return HospitalConsumption::create([
            'stay_id' => $stay->id,
            'product_id' => $product->id,
            'batch_id' => $batch?->id,
            'qty' => $qty,
            'source' => 'task',
            'created_by' => $user->id,
        ]);
    }
}
