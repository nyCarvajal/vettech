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
            'owner_id' => ['required', 'exists:owners,id'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'species_id' => ['required', 'exists:species,id'],
            'breed_id' => ['nullable', 'exists:breeds,id'],
            'sexo' => ['nullable', 'in:M,F,NA'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:tomorrow'],
            'color' => ['nullable', 'string', 'max:100'],
            'microchip' => ['nullable', 'string', 'max:100'],
            'peso_actual' => ['nullable', 'numeric', 'between:0,999.99'],
            'temperamento' => ['nullable', 'in:tranquilo,nervioso,agresivo,miedoso,otro'],
            'alergias' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'whatsapp' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\-\+\s\(\)]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
