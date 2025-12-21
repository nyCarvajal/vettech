<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max(255)',
            'type' => 'required|in:med,insumo,alimento,servicio',
            'sku' => 'nullable|string|max(255)',
            'unit' => 'required|string|max(50)',
            'requires_batch' => 'boolean',
            'min_stock' => 'required|integer|min:0',
            'sale_price' => 'required|numeric|min:0',
            'cost_avg' => 'nullable|numeric|min:0',
            'active' => 'boolean',
        ];
    }
}
