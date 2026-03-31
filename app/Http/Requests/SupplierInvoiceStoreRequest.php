<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierInvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'numero_factura' => ['required', 'string', 'max:60'],
            'fecha_factura' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_factura'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'impuestos' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', Rule::in(['borrador', 'confirmada'])],
            'observaciones' => ['nullable', 'string'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.item_id' => ['required', 'exists:items,id'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'detalles.*.costo_unitario' => ['required', 'numeric', 'min:0'],
            'detalles.*.precio_venta_unitario' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.descripcion' => ['nullable', 'string', 'max:250'],
            'detalles.*.es_obsequio' => ['nullable', 'boolean'],
        ];
    }
}
