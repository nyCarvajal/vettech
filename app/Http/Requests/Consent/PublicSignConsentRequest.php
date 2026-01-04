<?php

namespace App\Http\Requests\Consent;

use Illuminate\Foundation\Http\FormRequest;

class PublicSignConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signer_name' => ['required', 'string'],
            'signer_document' => ['nullable', 'string'],
            'signature_base64' => ['required', 'string'],
        ];
    }
}
