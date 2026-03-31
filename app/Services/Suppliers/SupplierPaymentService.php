<?php

namespace App\Services\Suppliers;

use App\Models\Banco;
use App\Models\Caja;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class SupplierPaymentService
{
    public function create(array $data, int $userId): SupplierPayment
    {
        return DB::connection('tenant')->transaction(function () use ($data, $userId) {
            $invoice = null;

            if (! empty($data['supplier_invoice_id'])) {
                $invoice = SupplierInvoice::query()->lockForUpdate()->findOrFail($data['supplier_invoice_id']);
                if ((float) $data['valor'] > (float) $invoice->saldo_pendiente) {
                    throw ValidationException::withMessages([
                        'valor' => 'El valor no puede exceder el saldo pendiente de la factura.',
                    ]);
                }
            }

            if ($data['origen_fondos'] === 'banco') {
                $banco = Banco::query()->lockForUpdate()->findOrFail($data['banco_id']);
                if ((float) $banco->saldo_actual < (float) $data['valor']) {
                    throw ValidationException::withMessages(['banco_id' => 'Saldo insuficiente en banco.']);
                }
                $banco->saldo_actual = (float) $banco->saldo_actual - (float) $data['valor'];
                $banco->save();
            }

            if ($data['origen_fondos'] === 'caja_menor') {
                $caja = Caja::query()->lockForUpdate()->findOrFail($data['caja_id']);
                if ((float) $caja->valor < (float) $data['valor']) {
                    throw ValidationException::withMessages(['caja_id' => 'Saldo insuficiente en caja menor.']);
                }
                $caja->valor = (float) $caja->valor - (float) $data['valor'];
                $caja->save();
            }

            $payment = SupplierPayment::create($data + [
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            if ($invoice) {
                $paid = (float) $invoice->total_pagado + (float) $payment->valor;
                $pending = max(0, (float) $invoice->total - $paid);

                $invoice->update([
                    'total_pagado' => $paid,
                    'saldo_pendiente' => $pending,
                    'estado_pago' => $pending <= 0 ? 'pagado' : ($paid > 0 ? 'parcial' : 'pendiente'),
                ]);
            }

            return $payment->fresh(['supplier', 'invoice']);
        });
    }
}
