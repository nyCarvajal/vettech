<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalStayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|integer',
            'cage_id' => 'required|exists:cages,id',
            'admitted_at' => 'required|date',
            'severity' => 'required|in:stable,observation,critical',
            'primary_dx' => 'nullable|string',
            'plan' => 'nullable|string',
            'diet' => 'nullable|string',
        ];
    }
}
