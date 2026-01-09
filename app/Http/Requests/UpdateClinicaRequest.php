<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClinicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-clinic-settings') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:300'],
            'nit' => ['nullable', 'string', 'max:100'],
            'dv' => ['nullable', 'string', 'max:2'],
            'regimen' => ['nullable', 'string', 'max:191'],
            'responsable_iva' => ['nullable', 'boolean'],
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:300'],
            'city' => ['nullable', 'string', 'max:191'],
            'department' => ['nullable', 'string', 'max:191'],
            'country' => ['nullable', 'string', 'max:2'],
            'website' => ['nullable', 'string', 'max:191'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'footer_note' => ['nullable', 'string'],
            'header_note' => ['nullable', 'string'],
            'payment_terms' => ['nullable', 'string'],
            'payment_due_days' => ['nullable', 'integer', 'min:0'],
            'invoice_prefix' => ['nullable', 'string', 'max:20'],
            'invoice_footer_legal' => ['nullable', 'string'],
            'default_tax_rate' => ['nullable', 'numeric', 'min:0'],
            'bank_account_info' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'dian_enabled' => ['nullable', 'boolean'],
            'dian_software_id' => ['nullable', 'string', 'max:191'],
            'dian_software_pin' => ['nullable', 'string', 'max:50'],
            'dian_test_set_id' => ['nullable', 'string', 'max:191'],
            'dian_resolution_prefix' => ['nullable', 'string', 'max:20'],
            'dian_resolution_number' => ['nullable', 'string', 'max:50'],
            'dian_resolution_from' => ['nullable', 'integer', 'min:0'],
            'dian_resolution_to' => ['nullable', 'integer', 'min:0'],
            'dian_resolution_date' => ['nullable', 'date'],
            'timezone' => ['nullable', 'string', 'max:191'],
            'currency' => ['nullable', 'string', 'max:10'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
