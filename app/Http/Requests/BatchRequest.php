<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'batch_code' => 'required|string|max(255)',
            'expires_at' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'qty_in' => 'required|integer|min:0',
        ];
    }
}
