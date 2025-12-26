<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stay_id' => ['required', 'exists:hospital_stays,id'],
            'day_id' => ['nullable', 'exists:hospital_days,id'],
            'source' => ['required', Rule::in(['service', 'inventory', 'manual'])],
            'product_id' => ['nullable', 'required_if:source,inventory', 'exists:products,id'],
            'description' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'created_by' => ['required', 'exists:users,id'],
        ];
    }
}
