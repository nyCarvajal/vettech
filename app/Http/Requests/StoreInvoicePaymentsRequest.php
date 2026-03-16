<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInvoicePaymentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.received' => ['nullable', 'numeric', 'min:0'],
            'payments.*.reference' => ['nullable', 'string'],
            'payments.*.card_type' => ['nullable', Rule::in(['credit', 'debit'])],
            'payments.*.bank_id' => ['nullable', 'exists:bancos,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $payments = $this->input('payments', []);

            foreach ($payments as $index => $payment) {
                if (($payment['method'] ?? null) === 'cash') {
                    $received = $payment['received'] ?? null;
                    if ($received === null) {
                        $validator->errors()->add("payments.{$index}.received", 'Debes registrar el valor recibido en efectivo.');
                        continue;
                    }

                    if ((float) $received < (float) ($payment['amount'] ?? 0)) {
                        $validator->errors()->add("payments.{$index}.received", 'El recibido en efectivo no puede ser menor al pago.');
                    }
                }

                if (($payment['method'] ?? null) === 'card' && empty($payment['card_type'])) {
                    $validator->errors()->add("payments.{$index}.card_type", 'Selecciona si es tarjeta crédito o débito.');
                }

                if (in_array(($payment['method'] ?? null), ['card', 'transfer'], true) && empty($payment['bank_id'])) {
                    $validator->errors()->add("payments.{$index}.bank_id", 'Selecciona el banco para este pago.');
                }
            }
        });
    }
}
