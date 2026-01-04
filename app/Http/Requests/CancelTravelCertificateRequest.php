<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelTravelCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cancel', $this->route('travel_certificate')) ?? false;
    }

    public function rules(): array
    {
        return [
            'canceled_reason' => ['required', 'string'],
        ];
    }
}
