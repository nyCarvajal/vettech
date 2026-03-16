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
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:1440',
            'authorized_roles' => 'nullable|array',
            'authorized_roles.*' => 'nullable|string|max:120',
            'cost_structure' => 'nullable|array',
            'cost_structure.*.item_id' => 'nullable|integer|exists:tenant.items,id',
            'cost_structure.*.quantity_available' => 'nullable|numeric|min:0',
            'cost_structure.*.unit_cost' => 'nullable|numeric|min:0',
            'cost_structure.*.quantity_used' => 'nullable|numeric|min:0',
            'cost_structure.*.application_cost' => 'nullable|numeric|min:0',
            'cost_structure_commission_percent' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
