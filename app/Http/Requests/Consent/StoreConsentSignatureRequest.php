<?php

namespace App\Http\Requests\Consent;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsentSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sign', $this->route('consent')) ?? false;
    }

    public function rules(): array
    {
        return [
            'signer_name' => ['required', 'string'],
            'signer_document' => ['nullable', 'string'],
            'signer_role' => ['required', 'string'],
            'signature_base64' => ['required', 'string'],
            'geo_hint' => ['nullable', 'string'],
        ];
    }
}
