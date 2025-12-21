<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => 'nullable|integer',
            'patient_id' => 'nullable|integer',
            'status' => 'in:open,paid,void',
        ];
    }
}
