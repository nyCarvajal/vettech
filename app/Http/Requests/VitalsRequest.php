<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VitalsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'measured_at' => ['required', 'date'],
            'temp' => ['nullable', 'numeric'],
            'hr' => ['nullable', 'integer'],
            'rr' => ['nullable', 'integer'],
            'spo2' => ['nullable', 'numeric'],
            'bp' => ['nullable', 'string', 'max:30'],
            'weight' => ['nullable', 'numeric'],
            'pain_scale' => ['nullable', 'integer'],
            'hydration' => ['nullable', 'string', 'max:30'],
            'mucous' => ['nullable', 'string', 'max:30'],
            'crt' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'measured_by' => ['required', 'exists:users,id'],
        ];
    }
}
