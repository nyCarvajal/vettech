<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:tenant.items,sku',
            'tipo' => 'nullable|string|max:120',
            'area' => 'nullable|exists:tenant.areas,id',
            'type' => 'required|in:product,service',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|numeric|min:0',
            'cantidad' => 'nullable|numeric|min:0',
            'inventariable' => 'boolean',
            'track_inventory' => 'boolean',
        ];
    }
}
