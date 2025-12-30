<?php

namespace App\Http\Requests;

use App\Models\Item;
use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientImmunizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $patientId = $this->input('paciente_id') ?? $this->route('paciente_id') ?? $this->route('patient');

        return Patient::find($patientId) !== null;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => ['required', Rule::exists('pacientes', 'id')],
            'consulta_id' => ['nullable', Rule::exists('historia_clinicas', 'id')],
            'applied_at' => ['required', 'date'],
            'vaccine_name' => ['required', 'string', 'max:255'],
            'contains_rabies' => ['boolean'],
            'item_id' => ['nullable', Rule::exists('items', 'id')],
            'item_manual' => ['nullable', 'string', 'max:255'],
            'batch_lot' => ['required', 'string', 'max:255'],
            'dose' => ['nullable', 'string', 'max:255'],
            'next_due_at' => ['nullable', 'date', 'after_or_equal:applied_at'],
            'expires_at' => ['nullable', 'date'],
            'vet_user_id' => ['nullable', Rule::exists('usuarios', 'id')],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['applied', 'scheduled', 'overdue'])],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $itemId = $this->input('item_id');
            $itemManual = $this->input('item_manual');

            if (! $itemId && ! $itemManual) {
                $validator->errors()->add('item_manual', 'Debes escribir el producto o seleccionar uno del inventario.');
            }

            if ($itemId && $itemManual) {
                $validator->errors()->add('item_manual', 'Cuando usas un producto del inventario no debes escribirlo manualmente.');
            }

            if ($itemId) {
                $item = Item::find($itemId);
                if (! $item) {
                    $validator->errors()->add('item_id', 'El producto seleccionado no existe en este tenant.');
                }
            }
        });
    }
}
