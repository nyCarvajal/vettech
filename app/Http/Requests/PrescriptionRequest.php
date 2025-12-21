<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|integer',
            'professional_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,signed,partial,done',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.dose' => 'required|string',
            'items.*.frequency' => 'required|string',
            'items.*.duration_days' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string',
            'items.*.qty_requested' => 'required|integer|min:1',
        ];
    }
}
