<?php

namespace App\Http\Requests\Consent;

use Illuminate\Foundation\Http\FormRequest;

class CreateConsentPublicLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('createPublicLink', $this->route('consent')) ?? false;
    }

    public function rules(): array
    {
        return [
            'expires_at' => ['nullable', 'date'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
