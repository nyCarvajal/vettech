<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $quantityRule = $this->routeIs('items.movements.adjust')
            ? 'required|numeric|not_in:0'
            : 'required|numeric|min:0.01';

        return [
            'quantity' => $quantityRule,
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
