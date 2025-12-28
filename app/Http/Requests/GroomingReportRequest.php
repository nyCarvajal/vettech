<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroomingReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fleas' => ['sometimes', 'boolean'],
            'ticks' => ['sometimes', 'boolean'],
            'skin_issue' => ['sometimes', 'boolean'],
            'ear_issue' => ['sometimes', 'boolean'],
            'observations' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fleas' => $this->boolean('fleas'),
            'ticks' => $this->boolean('ticks'),
            'skin_issue' => $this->boolean('skin_issue'),
            'ear_issue' => $this->boolean('ear_issue'),
        ]);
    }
}
