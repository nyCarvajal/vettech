<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdministrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'administered_at' => ['required', 'date'],
            'dose_given' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['done', 'skipped', 'late'])],
            'notes' => ['nullable', 'string'],
            'administered_by' => ['required', 'exists:users,id'],
        ];
    }
}
