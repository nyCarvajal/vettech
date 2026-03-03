<?php

namespace App\Services;

use App\Models\HospitalAdministration;
use App\Models\HospitalCharge;

class ChargeService
{
    public function createFromApplication(HospitalAdministration $application): HospitalCharge
    {
        $order = $application->order;
        $stay = $application->stay;
        $product = $order->product;

        $unitPrice = (float) ($product->sale_price ?? 0);
        $qty = 1;

        return HospitalCharge::create([
            'stay_id' => $stay->id,
            'patient_id' => $stay->patient_id,
            'day_id' => $application->day_id,
            'order_id' => $order->id,
            'application_id' => $application->id,
            'source' => $order->source === 'inventory' ? 'inventory' : 'service',
            'ref_type' => 'medicamento_aplicado',
            'ref_id' => $application->id,
            'product_id' => $order->product_id,
            'description' => sprintf('Aplicación: %s %s %s', $order->manual_name ?? $product?->name ?? 'Tratamiento', $order->dose ?? '', $order->route ?? ''),
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total' => $qty * $unitPrice,
            'status' => 'pending',
            'created_by' => $application->administered_by,
            'created_at' => $application->administered_at ?? now(),
        ]);
    }
}
