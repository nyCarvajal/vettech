<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier')?->id;

        return [
            'razon_social' => ['required', 'string', 'max:200'],
            'tipo_documento' => ['nullable', 'string', 'max:30'],
            'numero_documento' => ['nullable', 'string', 'max:50', Rule::unique('suppliers', 'numero_documento')->ignore($supplierId)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'celular' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'ciudad' => ['nullable', 'string', 'max:120'],
            'contacto_principal' => ['nullable', 'string', 'max:150'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
