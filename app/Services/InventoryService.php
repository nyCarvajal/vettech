<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    public function fefoPickBatch(Product $product): ?Batch
    {
        return $product->batches()
            ->where('qty_available', '>', 0)
            ->orderBy('expires_at')
            ->orderBy('id')
            ->first();
    }

    public function ensureNonNegative(Product $product, int $qty): void
    {
        if ($qty < 0) {
            throw new InvalidArgumentException('La cantidad debe ser positiva.');
        }

        $available = $product->requires_batch
            ? $product->batches()->sum('qty_available')
            : $product->stockMovements()->sum(DB::raw("CASE WHEN type='in' THEN qty WHEN type='out' OR type='merma' OR type='adjust' THEN -qty ELSE 0 END"));

        if ($available < $qty) {
            throw new InvalidArgumentException('No hay stock suficiente.');
        }
    }

    public function moveStock(Product $product, ?Batch $batch, string $type, int $qty, string $reason, ?string $refEntity, ?int $refId, User $user): StockMovement
    {
        if ($qty <= 0) {
            throw new InvalidArgumentException('Cantidad inválida');
        }

        if ($product->requires_batch && !$batch) {
            throw new InvalidArgumentException('Se requiere lote para este producto.');
        }

        return DB::transaction(function () use ($product, $batch, $type, $qty, $reason, $refEntity, $refId, $user) {
            if (in_array($type, ['out', 'merma']) && $product->requires_batch) {
                if ($batch->qty_available < $qty) {
                    throw new InvalidArgumentException('Stock insuficiente en el lote.');
                }
                $batch->qty_out += $qty;
                $batch->qty_available -= $qty;
                $batch->save();
            }

            if ($type === 'in' && $batch) {
                $batch->qty_in += $qty;
                $batch->qty_available += $qty;
                $batch->save();
            }

            if (!$product->requires_batch && in_array($type, ['out', 'merma'])) {
                $totalAvailable = $product->stockMovements()->whereIn('type', ['in', 'return'])->sum('qty')
                    - $product->stockMovements()->whereIn('type', ['out', 'merma', 'adjust'])->sum('qty');
                if ($totalAvailable < $qty) {
                    throw new InvalidArgumentException('Stock insuficiente.');
                }
            }

            return StockMovement::create([
                'product_id' => $product->id,
                'batch_id' => $batch?->id,
                'type' => $type,
                'qty' => $qty,
                'reason' => $reason,
                'ref_entity' => $refEntity,
                'ref_id' => $refId,
                'user_id' => $user->id,
            ]);
        });
    }
}
