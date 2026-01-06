<?php

namespace App\Http\Requests;

use App\Models\Followup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('followup'));
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['nullable', 'integer'],
            'patient_id' => ['nullable', 'exists:pacientes,id'],
            'owner_id' => ['nullable', 'exists:owners,id'],
            'consultation_id' => ['nullable', 'exists:encounters,id'],
            'followup_at' => ['required', 'date'],
            'performed_by' => ['nullable', 'string', 'max:255'],
            'performed_by_license' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:255'],
            'improved_status' => ['required', Rule::in(['yes', 'no', 'partial', 'unknown'])],
            'improved_score' => ['nullable', 'integer', 'min:0', 'max:10', 'required_unless:improved_status,unknown'],
            'observations' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'next_followup_at' => ['nullable', 'date'],
            'vitals.temperature_c' => ['nullable', 'numeric', 'between:30,45'],
            'vitals.heart_rate_bpm' => ['nullable', 'integer', 'min:0', 'max:400'],
            'vitals.respiratory_rate_rpm' => ['nullable', 'integer', 'min:0', 'max:200'],
            'vitals.weight_kg' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'vitals.hydration' => ['nullable', Rule::in(['normal', 'mild_dehydration', 'moderate', 'severe', 'unknown'])],
            'vitals.mucous_membranes' => ['nullable', Rule::in(['pink', 'pale', 'icteric', 'cyanotic', 'hyperemic', 'unknown'])],
            'vitals.capillary_refill_time_sec' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'vitals.pain_score_0_10' => ['nullable', 'integer', 'min:0', 'max:10'],
            'vitals.blood_pressure_sys' => ['nullable', 'integer', 'min:0'],
            'vitals.blood_pressure_dia' => ['nullable', 'integer', 'min:0'],
            'vitals.blood_pressure_map' => ['nullable', 'integer', 'min:0'],
            'vitals.o2_saturation_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'vitals.notes' => ['nullable', 'string'],
        ];
    }
}
