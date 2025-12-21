<?php

namespace App\Services;

use App\Models\Dispensation;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\HospitalStay;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function attachDispensationToSale(Dispensation $dispensation, ?Sale $sale = null): Sale
    {
        return DB::transaction(function () use ($dispensation, $sale) {
            $sale = $sale ?: Sale::create([
                'owner_id' => null,
                'patient_id' => $dispensation->prescription->patient_id,
                'created_by' => $dispensation->dispensed_by,
                'status' => 'open',
                'total' => 0,
            ]);

            foreach ($dispensation->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty_dispensed,
                    'unit_price' => $item->unit_price,
                    'discount' => 0,
                    'ref_entity' => 'dispensation',
                    'ref_id' => $dispensation->id,
                ]);
            }

            $sale->total = $sale->items()->sum(DB::raw('qty * unit_price - discount'));
            $sale->save();

            return $sale;
        });
    }

    public function createHospitalFinalBill(HospitalStay $stay): Sale
    {
        return DB::transaction(function () use ($stay) {
            $sale = Sale::create([
                'owner_id' => null,
                'patient_id' => $stay->patient_id,
                'created_by' => $stay->created_by,
                'status' => 'open',
                'total' => 0,
            ]);

            foreach ($stay->consumptions as $consumption) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $consumption->product_id,
                    'qty' => $consumption->qty,
                    'unit_price' => $consumption->product->sale_price,
                    'discount' => 0,
                    'ref_entity' => 'hospital_stay',
                    'ref_id' => $stay->id,
                ]);
            }

            $sale->total = $sale->items()->sum(DB::raw('qty * unit_price - discount'));
            $sale->save();

            return $sale;
        });
    }
}
