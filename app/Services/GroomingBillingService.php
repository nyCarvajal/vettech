<?php

namespace App\Services;

use App\Models\Grooming;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class GroomingBillingService
{
    public function createOrAttachSaleItem(Grooming $grooming): ?Sale
    {
        if (! Schema::hasTable('sales') || ! Schema::hasTable('sale_items')) {
            return null;
        }

        if (! $grooming->product_service_id) {
            throw new InvalidArgumentException('Se requiere seleccionar un servicio de producto para facturar.');
        }

        $price = $grooming->service_price ?? optional($grooming->serviceProduct)->sale_price ?? 0;

        return DB::transaction(function () use ($grooming, $price) {
            $sale = Sale::create([
                'owner_id' => $grooming->owner_id,
                'patient_id' => $grooming->patient_id,
                'created_by' => auth()->id() ?? $grooming->created_by,
                'status' => 'open',
                'total' => 0,
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $grooming->product_service_id,
                'qty' => 1,
                'unit_price' => $price,
                'discount' => 0,
                'ref_entity' => 'grooming',
                'ref_id' => $grooming->id,
            ]);

            $sale->total = $sale->items()->sum(DB::raw('qty * unit_price - discount'));
            $sale->save();

            return $sale;
        });
    }
}
