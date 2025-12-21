<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Dispensation;
use App\Models\DispensationItem;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DispenseService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function dispensePrescription(Prescription $prescription, array $items, User $user): Dispensation
    {
        if ($prescription->status === 'done') {
            throw new InvalidArgumentException('La fórmula ya está completada.');
        }

        return DB::transaction(function () use ($prescription, $items, $user) {
            $dispensation = Dispensation::create([
                'prescription_id' => $prescription->id,
                'dispensed_by' => $user->id,
                'dispensed_at' => now(),
                'status' => 'partial',
            ]);

            foreach ($items as $itemData) {
                /** @var Product $product */
                $product = Product::findOrFail($itemData['product_id']);
                $qty = (int) $itemData['qty'];
                $batch = null;

                if ($product->requires_batch) {
                    $batch = isset($itemData['batch_id']) ? Batch::findOrFail($itemData['batch_id']) : $this->inventoryService->fefoPickBatch($product);
                    if (!$batch) {
                        throw new InvalidArgumentException('No hay lote disponible.');
                    }
                }

                $this->inventoryService->ensureNonNegative($product, $qty);
                $this->inventoryService->moveStock($product, $batch, 'out', $qty, 'dispensacion', 'prescription', $prescription->id, $user);

                DispensationItem::create([
                    'dispensation_id' => $dispensation->id,
                    'product_id' => $product->id,
                    'batch_id' => $batch?->id,
                    'qty_dispensed' => $qty,
                    'unit_price' => $product->sale_price,
                    'cost_snapshot' => $batch?->cost ?? $product->cost_avg ?? 0,
                ]);
            }

            $totalDispensed = $prescription->dispensations()->with('items')->get()->flatMap->items->sum('qty_dispensed');
            $totalRequested = $prescription->items()->sum('qty_requested');
            $prescription->status = $totalDispensed >= $totalRequested ? 'done' : 'partial';
            $prescription->save();

            return $dispensation;
        });
    }
}
