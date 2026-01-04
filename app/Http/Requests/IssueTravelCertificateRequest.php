<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueTravelCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('issue', $this->route('travel_certificate')) ?? false;
    }

    public function rules(): array
    {
        return [
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
