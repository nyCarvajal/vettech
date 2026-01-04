<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkSignedConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consent_document_id' => ['required', 'exists:consent_documents,id'],
        ];
    }
}
