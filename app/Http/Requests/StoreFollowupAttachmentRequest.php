<?php

namespace App\Http\Requests;

use App\Models\Followup;
use Illuminate\Foundation\Http\FormRequest;

class StoreFollowupAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Followup $followup */
        $followup = $this->route('followup');

        return $this->user()->can('addAttachment', $followup);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ];
    }
}
