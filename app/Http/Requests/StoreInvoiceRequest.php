<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_id' => ['nullable', 'exists:items,id'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'in:cash,card,transfer,mixed'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.received' => ['nullable', 'numeric', 'min:0'],
            'payments.*.reference' => ['nullable', 'string'],
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

            $payments = $this->input('payments', []);

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
        });
    }
}
