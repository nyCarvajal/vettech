<?php

namespace App\Http\Requests\Consent;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsentDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ConsentDocument::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'template_id' => ['required', 'exists:consent_templates,id'],
            'owner_snapshot' => ['nullable', 'array'],
            'pet_snapshot' => ['nullable', 'array'],
        ];
    }
}
