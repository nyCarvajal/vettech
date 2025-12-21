<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cash_session_id' => 'required|exists:cash_sessions,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,transfer',
            'reason' => 'required|string|max(255)',
        ];
    }
}
