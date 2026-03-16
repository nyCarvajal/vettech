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
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:1440',
            'authorized_roles' => 'nullable|array',
            'authorized_roles.*' => 'nullable|string|max:120',
            'cost_structure' => 'nullable|array',
            'cost_structure.*.product_id' => 'nullable|integer|exists:products,id',
            'cost_structure.*.quantity_available' => 'nullable|numeric|min:0',
            'cost_structure.*.unit_cost' => 'nullable|numeric|min:0',
            'cost_structure.*.quantity_used' => 'nullable|numeric|min:0',
            'cost_structure.*.application_cost' => 'nullable|numeric|min:0',
            'cost_structure_commission_percent' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
