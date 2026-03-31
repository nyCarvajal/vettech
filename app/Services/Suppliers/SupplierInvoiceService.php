<?php

namespace App\Services\Suppliers;

use App\Models\InventoryMovement;
use App\Models\Item;
use App\Models\SupplierInvoice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SupplierInvoiceService
{
    public function create(array $data, int $userId): SupplierInvoice
    {
        return DB::connection('tenant')->transaction(function () use ($data, $userId) {
            $invoice = SupplierInvoice::create([
                'supplier_id' => $data['supplier_id'],
                'numero_factura' => $data['numero_factura'],
                'fecha_factura' => $data['fecha_factura'],
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
                'descuento' => (float) ($data['descuento'] ?? 0),
                'impuestos' => (float) ($data['impuestos'] ?? 0),
                'estado' => $data['estado'],
                'observaciones' => $data['observaciones'] ?? null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $subtotal = 0;
            foreach ($data['detalles'] as $line) {
                $esObsequio = (bool) Arr::get($line, 'es_obsequio', false);
                $cantidad = (float) $line['cantidad'];
                $costo = (float) $line['costo_unitario'];
                $lineSubtotal = $esObsequio ? 0 : round($cantidad * $costo, 2);

                $invoice->details()->create([
                    'item_id' => $line['item_id'],
                    'descripcion' => $line['descripcion'] ?? null,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo,
                    'precio_venta_unitario' => $line['precio_venta_unitario'] ?? null,
                    'subtotal' => $lineSubtotal,
                    'es_obsequio' => $esObsequio,
                    'afecta_valor' => ! $esObsequio,
                ]);

                $subtotal += $lineSubtotal;
            }

            $total = max(0, $subtotal - (float) $invoice->descuento + (float) $invoice->impuestos);
            $invoice->forceFill([
                'subtotal' => round($subtotal, 2),
                'total' => round($total, 2),
                'saldo_pendiente' => round($total, 2),
                'estado_pago' => $total > 0 ? 'pendiente' : 'pagado',
            ])->save();

            if ($invoice->estado === 'confirmada') {
                $this->applyInventory($invoice, $userId);
            }

            return $invoice->fresh(['supplier', 'details.item']);
        });
    }

    public function cancel(SupplierInvoice $invoice, int $userId): void
    {
        DB::connection('tenant')->transaction(function () use ($invoice, $userId) {
            if ($invoice->estado !== 'confirmada') {
                $invoice->update(['estado' => 'anulada', 'updated_by' => $userId]);
                return;
            }

            foreach ($invoice->details as $detail) {
                $item = Item::query()->lockForUpdate()->findOrFail($detail->item_id);
                $beforeStock = (float) ($item->stock ?? 0);
                $afterStock = max(0, $beforeStock - (float) $detail->cantidad);

                $item->stock = $afterStock;
                $item->save();

                InventoryMovement::create([
                    'item_id' => $item->id,
                    'movement_type' => 'exit',
                    'quantity' => $detail->cantidad,
                    'unit_cost' => $detail->costo_unitario,
                    'before_stock' => $beforeStock,
                    'after_stock' => $afterStock,
                    'reference' => "ANULACIÓN FC {$invoice->numero_factura}",
                    'notes' => 'Reversión por anulación de factura de proveedor',
                    'related_type' => SupplierInvoice::class,
                    'related_id' => $invoice->id,
                    'user_id' => $userId,
                    'occurred_at' => now(),
                ]);
            }

            $invoice->update([
                'estado' => 'anulada',
                'updated_by' => $userId,
            ]);
        });
    }

    private function applyInventory(SupplierInvoice $invoice, int $userId): void
    {
        foreach ($invoice->details as $detail) {
            $item = Item::query()->lockForUpdate()->findOrFail($detail->item_id);
            $beforeStock = (float) ($item->stock ?? 0);
            $afterStock = $beforeStock + (float) $detail->cantidad;

            // Lógica crítica: confirmación de compra actualiza stock/costos en una única transacción.
            $item->stock = $afterStock;
            $item->cost_price = $detail->costo_unitario;
            $item->costo = $detail->costo_unitario;

            if ($detail->precio_venta_unitario !== null) {
                $item->sale_price = $detail->precio_venta_unitario;
                $item->valor = $detail->precio_venta_unitario;
            }

            $item->save();

            InventoryMovement::create([
                'item_id' => $item->id,
                'movement_type' => 'entry',
                'quantity' => $detail->cantidad,
                'unit_cost' => $detail->costo_unitario,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference' => "FC {$invoice->numero_factura}",
                'notes' => $detail->es_obsequio ? 'Entrada por obsequio' : 'Entrada por compra proveedor',
                'related_type' => SupplierInvoice::class,
                'related_id' => $invoice->id,
                'user_id' => $userId,
                'occurred_at' => now(),
            ]);
        }
    }
}
