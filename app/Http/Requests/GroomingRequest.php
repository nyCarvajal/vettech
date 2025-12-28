<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroomingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:pacientes,id'],
            'owner_id' => ['required', 'exists:owners,id'],
            'scheduled_at' => ['required', 'date'],
            'indications' => ['nullable', 'string'],
            'needs_pickup' => ['boolean'],
            'pickup_address' => ['required_if:needs_pickup,1', 'nullable', 'string', 'max:255'],
            'external_deworming' => ['boolean'],
            'deworming_source' => ['required_if:external_deworming,1', 'in:none,manual,inventory'],
            'deworming_product_id' => ['required_if:deworming_source,inventory', 'nullable', 'exists:products,id'],
            'deworming_product_name' => ['required_if:deworming_source,manual', 'nullable', 'string', 'max:255'],
            'product_service_id' => ['nullable', 'exists:products,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'needs_pickup' => $this->boolean('needs_pickup'),
            'external_deworming' => $this->boolean('external_deworming'),
            'service_source' => $this->input('product_service_id') ? 'product' : 'none',
        ]);
    }
}
