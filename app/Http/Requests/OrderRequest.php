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
            'day_id' => ['nullable', 'exists:hospital_days,id'],
            'type' => ['required', Rule::in(['medication', 'procedure', 'feeding', 'fluid', 'other'])],
            'source' => ['required', Rule::in(['inventory', 'manual'])],
            'product_id' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('source') === 'inventory' && blank($this->input('manual_name'))),
                'exists:products,id',
            ],
            'manual_name' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('source') === 'manual' || ($this->input('source') === 'inventory' && blank($this->input('product_id')))),
                'string',
            ],
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
