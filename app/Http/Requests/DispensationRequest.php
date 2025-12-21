<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DispensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_id' => 'nullable|exists:batches,id',
            'items.*.qty' => 'required|integer|min:1',
        ];
    }
}
