<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcedureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:surgery,procedure'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,in_progress,completed,canceled'],
            'scheduled_at' => ['nullable', 'date', 'required_if:status,scheduled'],
            'started_at' => ['nullable', 'date', 'required_if:status,in_progress,completed'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'patient_snapshot' => ['required'],
            'owner_snapshot' => ['nullable'],
            'assistants' => ['nullable', 'array'],
            'consent_document_id' => ['nullable', 'exists:consent_documents,id'],
            'currency' => ['nullable', 'string', 'max:8'],
            'anesthesia_medications' => ['nullable', 'array'],
            'anesthesia_medications.*.drug_name' => ['required_with:anesthesia_medications', 'string', 'max:255'],
            'anesthesia_medications.*.dose' => ['nullable', 'string', 'max:255'],
            'anesthesia_medications.*.dose_unit' => ['nullable', 'string', 'max:50'],
            'anesthesia_medications.*.route' => ['nullable', 'string', 'max:50'],
            'anesthesia_medications.*.frequency' => ['nullable', 'string', 'max:100'],
            'anesthesia_medications.*.notes' => ['nullable', 'string'],
        ];
    }
}
