<?php

namespace App\Services\Inventory;

use App\Models\InventoryMovement;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    protected array $movementTypeCache = [];

    public function createItemWithInitialStock(array $data): Item
    {
        return DB::connection('tenant')->transaction(function () use ($data) {
            $prepared = $this->prepareItemData($data);

            $initialStock = (float) ($prepared['stock'] ?? 0);
            $prepared['stock'] = $this->shouldTrackInventoryData($prepared) ? 0 : $initialStock;

            $item = Item::create($prepared);

            if ($this->shouldTrackInventory($item) && $initialStock > 0) {
                $this->recordMovement($item, 'initial', $initialStock, [
                    'reference' => $data['reference'] ?? 'Stock inicial',
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            return $item;
        });
    }

    public function addEntry(Item $item, float $qty, array $data = []): InventoryMovement
    {
        return $this->recordMovement($item, 'entry', $qty, $data);
    }

    public function addExit(Item $item, float $qty, array $data = []): InventoryMovement
    {
        return $this->recordMovement($item, 'exit', -abs($qty), $data);
    }

    public function addAdjust(Item $item, float $qty, array $data = []): InventoryMovement
    {
        return $this->recordMovement($item, 'adjust', $qty, $data);
    }

    public function prepareItemData(array $data): array
    {
        $salePrice = $data['sale_price'] ?? $data['valor'] ?? null;
        $costPrice = $data['cost_price'] ?? $data['costo'] ?? null;

        $data['sale_price'] = $salePrice;
        $data['cost_price'] = $costPrice;
        $data['valor'] = $salePrice;
        $data['costo'] = $costPrice;

        if (($data['type'] ?? 'product') === 'service') {
            $data['inventariable'] = false;
            $data['track_inventory'] = false;
        }

        return $data;
    }

    protected function recordMovement(Item $item, string $type, float $delta, array $data = []): InventoryMovement
    {
        $connection = $item->getConnectionName() ?? 'tenant';

        return DB::connection($connection)->transaction(function () use ($item, $type, $delta, $data) {
            $lockedItem = Item::query()
                ->whereKey($item->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) ($lockedItem->stock ?? 0);
            $after = $before + $delta;

            if ($this->shouldTrackInventory($lockedItem) && $after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'El movimiento dejaría el stock en negativo.',
                ]);
            }

            $lockedItem->stock = $after;
            $lockedItem->save();

            $movementQuantity = $type === 'adjust' ? $delta : abs($delta);

            $resolvedType = $this->resolveMovementType($connection, $type);
            $meta = $data['meta'] ?? null;
            if ($resolvedType !== $type) {
                $meta = array_merge($meta ?? [], [
                    'original_movement_type' => $type,
                ]);
            }

            return InventoryMovement::create([
                'item_id' => $lockedItem->id,
                'movement_type' => $resolvedType,
                'quantity' => $movementQuantity,
                'unit_cost' => $lockedItem->cost_price,
                'before_stock' => $before,
                'after_stock' => $after,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'related_type' => $data['related_type'] ?? 'manual',
                'related_id' => $data['related_id'] ?? 0,
                'user_id' => $data['user_id'] ?? auth()->id() ?? 0,
                'occurred_at' => $data['occurred_at'] ?? now(),
                'meta' => $meta,
            ]);
        });
    }

    protected function shouldTrackInventory(Item $item): bool
    {
        return (bool) ($item->inventariable || $item->track_inventory);
    }

    protected function shouldTrackInventoryData(array $data): bool
    {
        return (bool) (($data['inventariable'] ?? false) || ($data['track_inventory'] ?? false));
    }

    protected function resolveMovementType(string $connection, string $type): string
    {
        $supported = $this->movementTypeCache[$connection] ?? null;

        if (! $supported) {
            $columns = DB::connection($connection)
                ->select("SHOW COLUMNS FROM inventory_movements LIKE 'movement_type'");

            $supported = [];
            if (! empty($columns)) {
                $columnType = $columns[0]->Type ?? '';
                if (preg_match("/^enum\\((.*)\\)$/", $columnType, $matches)) {
                    $supported = array_map(
                        fn ($value) => trim($value, "'"),
                        explode(',', $matches[1])
                    );
                }
            }

            $this->movementTypeCache[$connection] = $supported;
        }

        if (in_array($type, $supported, true)) {
            return $type;
        }

        return 'adjustment';
    }
}
