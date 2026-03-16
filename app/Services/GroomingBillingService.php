<?php

namespace App\Services;

use App\Models\Grooming;
use App\Models\OrdenDeCompra;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class GroomingBillingService
{
    public function createOrAttachSaleItem(Grooming $grooming): ?Venta
    {
        if (! Schema::hasTable('orden_de_compras') || ! Schema::hasTable('ventas')) {
            return null;
        }

        $itemId = $grooming->service_source === 'item' ? $grooming->service_id : $grooming->product_service_id;

        if (! $itemId) {
            throw new InvalidArgumentException('Se requiere seleccionar un servicio para facturar.');
        }

        $price = (float) ($grooming->service_price ?? 0);

        return DB::transaction(function () use ($grooming, $itemId, $price) {
            $order = OrdenDeCompra::create([
                'fecha_hora' => now(),
                'responsable' => auth()->id() ?? $grooming->created_by,
                'paciente' => $grooming->patient_id,
                'activa' => true,
            ]);

            return Venta::create([
                'cuenta' => $order->id,
                'producto' => $itemId,
                'cantidad' => 1,
                'descuento' => 0,
                'valor_unitario' => $price,
                'valor_total' => $price,
                'usuario_id' => auth()->id() ?? $grooming->created_by,
            ]);
        });
    }
}
