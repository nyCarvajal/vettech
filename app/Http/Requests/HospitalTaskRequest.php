<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HospitalTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stay_id' => 'required|exists:hospital_stays,id',
            'category' => 'required|in:med,fluidos,alimento,control,procedimiento',
            'title' => 'required|string|max(255)',
            'instructions' => 'nullable|string',
            'times_json' => 'required|array|min:1',
            'times_json.*' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date',
        ];
    }
}
