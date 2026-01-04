<?php

namespace App\Http\Requests\Consent;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\PlaceholderService;

class StoreConsentTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ConsentTemplate::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'body_html' => ['required', 'string'],
            'allowed_placeholders' => ['nullable', 'array'],
            'required_signers' => ['nullable', 'array'],
            'requires_pet' => ['boolean'],
            'requires_owner' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = new PlaceholderService();
        $allowed = $this->input('allowed_placeholders', []);
        $invalid = $service->validatePlaceholders($this->input('body_html', ''), $allowed ?: null);

        if (count($invalid) > 0) {
            $this->failedValidation(validator([], []));
        }
    }
}
