<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['required', 'exists:owners,id'],
            'notes' => ['nullable', 'string'],
            'is_credit' => ['nullable', 'boolean'],
            'credit_days' => ['nullable', Rule::in([5, 10, 15, 30, 60])],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_id' => ['nullable', 'exists:items,id'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payments' => [Rule::requiredIf(fn () => ! $this->boolean('is_credit')), 'array'],
            'payments.*.method' => ['required_with:payments', 'in:cash,card,transfer,mixed'],
            'payments.*.amount' => ['required_with:payments.*.method', 'numeric', 'min:0.01'],
            'payments.*.received' => ['nullable', 'numeric', 'min:0'],
            'payments.*.reference' => ['nullable', 'string'],
            'payments.*.card_type' => ['nullable', Rule::in(['credit', 'debit'])],
            'payments.*.bank_id' => ['nullable', 'exists:bancos,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $lines = $this->input('lines', []);
            foreach ($lines as $index => $line) {
                if (empty($line['item_id']) && empty($line['description'])) {
                    $validator->errors()->add("lines.{$index}.description", 'Debe indicar una descripción para la línea manual.');
                }
            }

            $isCredit = $this->boolean('is_credit');
            if ($isCredit && empty($this->input('credit_days'))) {
                $validator->errors()->add('credit_days', 'Selecciona el plazo del crédito.');
            }
            $payments = $this->input('payments', []);

            if (! $isCredit && count($payments) === 0) {
                $validator->errors()->add('payments', 'Debes registrar al menos un pago.');
            }

            foreach ($payments as $index => $payment) {
                if (($payment['method'] ?? null) !== 'cash') {
                    continue;
                }

                $received = $payment['received'] ?? null;
                if ($received === null) {
                    $validator->errors()->add("payments.{$index}.received", 'Debes registrar el valor recibido en efectivo.');
                    continue;
                }

                if ((float) $received < (float) ($payment['amount'] ?? 0)) {
                    $validator->errors()->add("payments.{$index}.received", 'El recibido en efectivo no puede ser menor al pago.');
                }
            }

            foreach ($payments as $index => $payment) {
                if (($payment['method'] ?? null) === 'card') {
                    if (empty($payment['card_type'])) {
                        $validator->errors()->add("payments.{$index}.card_type", 'Selecciona si es tarjeta crédito o débito.');
                    }
                }

                if (in_array(($payment['method'] ?? null), ['card', 'transfer'], true)) {
                    if (empty($payment['bank_id'])) {
                        $validator->errors()->add("payments.{$index}.bank_id", 'Selecciona el banco para este pago.');
                    }
                }
            }
        });
    }
}
