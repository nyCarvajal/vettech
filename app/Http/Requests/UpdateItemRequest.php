<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Item $item */
        $item = $this->route('item');

        return [
            'nombre' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:tenant.items,sku,' . $item?->id,
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
