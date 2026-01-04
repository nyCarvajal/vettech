<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('travel_certificate')) ?? false;
    }

    public function rules(): array
    {
        $rules = (new StoreTravelCertificateRequest())->rules();
        $rules['status'] = ['sometimes', 'in:draft,issued,canceled'];
        return $rules;
    }
}
