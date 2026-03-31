<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierPaymentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_invoice_id' => ['nullable', 'exists:supplier_invoices,id'],
            'fecha_pago' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'metodo_pago' => ['nullable', 'string', 'max:40'],
            'origen_fondos' => ['required', Rule::in(['caja_menor', 'banco'])],
            'caja_id' => ['nullable', 'required_if:origen_fondos,caja_menor', 'exists:cajas,id'],
            'banco_id' => ['nullable', 'required_if:origen_fondos,banco', 'exists:bancos,id'],
            'referencia' => ['nullable', 'string', 'max:80'],
            'observaciones' => ['nullable', 'string'],
        ];
    }
}
