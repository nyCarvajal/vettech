<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['nullable', 'exists:owners,id'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'species_id' => ['required', 'exists:species,id'],
            'breed_id' => ['nullable', 'exists:breeds,id', 'required_without:breed_name'],
            'breed_name' => ['nullable', 'string', 'max:255', 'required_without:breed_id'],
            'sexo' => ['nullable', 'in:M,F,NA'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:tomorrow'],
            'color' => ['nullable', 'string', 'max:100'],
            'microchip' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:100'],
            'peso_actual' => ['nullable', 'numeric', 'between:0,200000'],
            'weight_unit' => ['nullable', 'in:kg,g', 'required_with:peso_actual'],
            'temperamento' => ['nullable', 'in:tranquilo,nervioso,agresivo,miedoso,otro'],
            'alergias' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-\+\s\(\)]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'age_value' => ['nullable', 'integer', 'min:0', 'required_with:age_unit'],
            'age_unit' => ['nullable', 'in:years,months', 'required_with:age_value'],
            'tutores_json' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasOwner = $this->filled('owner_id');
            $payload = $this->tutoresPayload();

            if (! $hasOwner && count($payload) === 0) {
                $validator->errors()->add('tutores_json', 'Debes registrar al menos un tutor.');
            }
        });
    }

    private function tutoresPayload(): array
    {
        $raw = $this->input('tutores_json');

        if (! $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
