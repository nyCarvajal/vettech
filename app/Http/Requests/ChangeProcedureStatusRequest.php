<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeProcedureStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:scheduled,in_progress,completed,canceled'],
            'scheduled_at' => ['nullable', 'date', 'required_if:status,scheduled'],
            'started_at' => ['nullable', 'date', 'required_if:status,in_progress,completed'],
            'ended_at' => ['nullable', 'date', 'required_if:status,completed', 'after_or_equal:started_at'],
        ];
    }
}
