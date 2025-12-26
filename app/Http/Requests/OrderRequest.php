<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            'type' => ['required', Rule::in(['medication', 'procedure', 'feeding', 'fluid', 'other'])],
            'source' => ['required', Rule::in(['inventory', 'manual'])],
            'product_id' => ['nullable', 'required_if:source,inventory', 'exists:products,id'],
            'manual_name' => ['nullable', 'required_if:source,manual', 'string'],
            'dose' => ['nullable', 'string', 'max:80'],
            'route' => ['nullable', 'string', 'max:50'],
            'frequency' => ['nullable', 'string', 'max:50'],
            'schedule_json' => ['nullable', 'array'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'instructions' => ['nullable', 'string'],
            'created_by' => ['required', 'exists:users,id'],
        ];
    }
}
