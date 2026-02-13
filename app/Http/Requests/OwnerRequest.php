<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $whatsappPrefix = $this->input('whatsapp_prefix');
        $whatsappNumber = $this->input('whatsapp_number');

        if ($whatsappNumber) {
            $prefixedWhatsapp = trim(($whatsappPrefix ?: '') . ' ' . $whatsappNumber);
            $this->merge(['whatsapp' => $prefixedWhatsapp]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-\+\s\(\)]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-\+\s\(\)]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8'],
            'generate_random_password' => ['nullable', 'boolean'],
            'send_credentials' => ['nullable', 'boolean'],
            'whatsapp_prefix' => ['nullable', 'string', 'max:5'],
            'whatsapp_number' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-\+\s\(\)]+$/'],
            'document_type_id' => ['nullable', 'exists:tipo_identificacions,id'],
            'document' => ['nullable', 'string', 'max:100'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'municipio_id' => ['nullable', 'exists:municipios,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
