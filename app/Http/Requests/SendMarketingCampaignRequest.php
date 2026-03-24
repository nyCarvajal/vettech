<?php

namespace App\Http\Requests;

use App\Services\Marketing\InactivePatientCampaignService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMarketingCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'campaign_type' => ['required', Rule::in(array_keys(app(InactivePatientCampaignService::class)->campaignTypes()))],
            'message_template' => ['required', 'string', 'max:5000'],
            'patient_ids' => ['required', 'array', 'min:1'],
            'patient_ids.*' => ['integer', 'distinct', 'exists:pacientes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_ids.required' => 'Selecciona al menos un paciente para enviar la campaña.',
            'patient_ids.min' => 'Selecciona al menos un paciente para enviar la campaña.',
        ];
    }
}
