<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'owner_id' => ['required', 'exists:owners,id'],
            'cage_id' => ['nullable', 'exists:cages,id'],
            'admitted_at' => ['required', 'date'],
            'severity' => ['required', 'in:stable,observation,critical'],
            'primary_dx' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'diet' => ['nullable', 'string'],
            'created_by' => ['required', 'exists:users,id'],
        ];
    }
}
