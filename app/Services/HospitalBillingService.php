<?php

namespace App\Services;

use App\Models\HospitalCharge;
use App\Models\HospitalStay;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class HospitalBillingService
{
    public function addCharge(array $data): HospitalCharge
    {
        $data['total'] = ($data['qty'] ?? 1) * ($data['unit_price'] ?? 0);
        $data['created_at'] = $data['created_at'] ?? now();

        return HospitalCharge::create($data);
    }

    public function generateInvoice(HospitalStay $stay): Sale
    {
        return DB::transaction(function () use ($stay) {
            $sale = Sale::create([
                'owner_id' => $stay->owner_id,
                'patient_id' => $stay->patient_id,
                'created_by' => $stay->created_by,
                'total' => 0,
                'status' => 'open',
            ]);

            $total = 0;
            foreach ($stay->charges as $charge) {
                $lineTotal = $charge->total ?? ($charge->qty * $charge->unit_price);
                $total += $lineTotal;
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $charge->product_id,
                    'qty' => $charge->qty,
                    'unit_price' => $charge->unit_price,
                    'discount' => 0,
                    'ref_entity' => 'hospital_charges',
                    'ref_id' => $charge->id,
                ]);
            }

            $sale->update(['total' => $total]);

            return $sale->fresh(['items']);
        });
    }
}
