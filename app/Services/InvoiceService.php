<?php

namespace App\Services;

use App\Models\BillingSetting;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Item;
use App\Services\Pricing\TaxCalculator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function __construct(private readonly TaxCalculator $calculator)
    {
    }

    public function createInvoice(array $data): Invoice
    {
        return DB::connection('tenant')->transaction(function () use ($data) {
            $defaults = $this->billingDefaults();
            $prefix = Arr::get($data, 'prefix', $defaults['pos_prefix']);
            $invoiceType = Arr::get($data, 'invoice_type', 'pos');
            $number = $this->nextNumber($prefix);
            $fullNumber = $prefix ? sprintf('%s-%07d', $prefix, $number) : (string) $number;
            $issuedAt = Arr::get($data, 'issued_at', now());

            $lines = $this->buildLines($data['lines'] ?? [], $defaults);
            $totals = $this->recalculateTotals($lines);

            $isCredit = (bool) Arr::get($data, 'is_credit', false);
            $creditDays = $isCredit ? (int) Arr::get($data, 'credit_days', 0) : null;
            $dueAt = $isCredit && $creditDays > 0 ? Carbon::parse($issuedAt)->addDays($creditDays) : null;

            $invoice = Invoice::create([
                'invoice_type' => $invoiceType,
                'prefix' => $prefix,
                'number' => $number,
                'full_number' => $fullNumber,
                'owner_id' => $data['owner_id'],
                'user_id' => $data['user_id'] ?? auth()->id(),
                'status' => 'issued',
                'currency' => $defaults['currency'],
                'issued_at' => $issuedAt,
                'notes' => Arr::get($data, 'notes'),
                'is_credit' => $isCredit,
                'credit_days' => $creditDays,
                'due_at' => $dueAt,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'tax_total' => $totals['tax_total'],
                'commission_total' => $totals['commission_total'],
                'total' => $totals['total'],
                'paid_total' => 0,
                'change_total' => 0,
            ]);

            $invoice->lines()->createMany($lines);

            $paymentTotals = $isCredit
                ? ['paid_total' => 0, 'change_total' => 0, 'status' => 'issued']
                : $this->applyPayments($invoice, $data['payments'] ?? []);

            $invoice->update([
                'paid_total' => $paymentTotals['paid_total'],
                'change_total' => $paymentTotals['change_total'],
                'status' => $paymentTotals['status'],
            ]);

            $this->decrementInventory($invoice);

            return $invoice->refresh();
        });
    }

    public function voidInvoice(Invoice $invoice, ?string $reason = null): Invoice
    {
        return DB::connection('tenant')->transaction(function () use ($invoice, $reason) {
            if ($invoice->status === 'void') {
                return $invoice;
            }

            foreach ($invoice->lines as $line) {
                if (! $line->affects_inventory || ! $line->item_id) {
                    continue;
                }

                $item = Item::query()->lockForUpdate()->find($line->item_id);
                if (! $item) {
                    continue;
                }

                $item->stock = $item->stock + $line->inventory_qty_out;
                $item->save();

                InventoryMovement::create([
                    'item_id' => $item->id,
                    'movement_type' => 'sale_void',
                    'quantity' => $line->inventory_qty_out,
                    'unit_cost' => $item->cost_price ?? $item->costo,
                    'related_type' => Invoice::class,
                    'related_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'occurred_at' => now(),
                    'meta' => ['reason' => $reason],
                ]);
            }

            $invoice->status = 'void';
            $invoice->notes = $reason ? trim(($invoice->notes ?? '') . "\nAnulación: {$reason}") : $invoice->notes;
            $invoice->save();

            return $invoice->refresh();
        });
    }

    private function buildLines(array $lines, array $defaults): array
    {
        if (count($lines) === 0) {
            throw ValidationException::withMessages(['lines' => 'Debe agregar al menos una línea a la factura.']);
        }

        return collect($lines)->map(function ($line) use ($defaults) {
            $item = null;
            if (! empty($line['item_id'])) {
                $item = Item::query()->find($line['item_id']);
            }

            $quantity = (float) ($line['quantity'] ?? 0);
            $unitPrice = (float) ($line['unit_price'] ?? $item?->sale_price ?? $item?->valor ?? 0);
            $discountRate = $this->normalizeRate($line['discount_rate'] ?? 0);
            $taxRate = $this->normalizeRate($line['tax_rate'] ?? $defaults['default_tax_rate']);
            $commissionRate = $this->normalizeRate($line['commission_rate'] ?? $defaults['default_commission_rate']);

            $calculated = $this->calculator->calculateLine(
                $quantity,
                $unitPrice,
                $discountRate,
                $taxRate,
                $commissionRate
            );

            $affectsInventory = $item && $item->track_inventory && $item->type === 'product';

            return [
                'item_id' => $item?->id,
                'description' => $line['description'] ?? $item?->nombre ?? 'Línea manual',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_rate' => $discountRate,
                'discount_amount' => $calculated['discount_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $calculated['tax_amount'],
                'commission_rate' => $commissionRate,
                'commission_amount' => $calculated['commission_amount'],
                'line_subtotal' => $calculated['line_subtotal'],
                'line_total' => $calculated['line_total'],
                'affects_inventory' => $affectsInventory,
                'inventory_qty_out' => $affectsInventory ? $quantity : 0,
            ];
        })->all();
    }

    private function recalculateTotals(array $lines): array
    {
        $subtotal = collect($lines)->sum('line_subtotal');
        $discountTotal = collect($lines)->sum('discount_amount');
        $taxTotal = collect($lines)->sum('tax_amount');
        $commissionTotal = collect($lines)->sum('commission_amount');
        $total = ($subtotal - $discountTotal) + $taxTotal + $commissionTotal;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'commission_total' => round($commissionTotal, 2),
            'total' => round($total, 2),
        ];
    }

    private function applyPayments(Invoice $invoice, array $payments): array
    {
        if (count($payments) === 0) {
            return [
                'paid_total' => 0,
                'change_total' => 0,
                'status' => 'issued',
            ];
        }

        $paidTotal = 0;
        $changeTotal = 0;

        foreach ($payments as $payment) {
            $amount = (float) ($payment['amount'] ?? 0);
            $received = isset($payment['received']) ? (float) $payment['received'] : null;
            $change = 0;

            if (($payment['method'] ?? '') === 'cash') {
                if ($received === null) {
                    $received = $amount;
                }
                $change = max(0, $received - $amount);
            }

            $paidTotal += $amount;
            $changeTotal += $change;

            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'method' => $payment['method'],
                'amount' => $amount,
                'received' => $received,
                'change' => $change,
                'reference' => $payment['reference'] ?? null,
                'card_type' => $payment['card_type'] ?? null,
                'bank_id' => $payment['bank_id'] ?? null,
                'paid_at' => $payment['paid_at'] ?? now(),
                'meta' => $payment['meta'] ?? null,
            ]);
        }

        $status = $paidTotal >= $invoice->total ? 'paid' : 'issued';

        return [
            'paid_total' => round($paidTotal, 2),
            'change_total' => round($changeTotal, 2),
            'status' => $status,
        ];
    }

    private function decrementInventory(Invoice $invoice): void
    {
        foreach ($invoice->lines as $line) {
            if (! $line->affects_inventory || ! $line->item_id) {
                continue;
            }

            $item = Item::query()->lockForUpdate()->find($line->item_id);

            if (! $item) {
                continue;
            }

            $item->stock = $item->stock - $line->inventory_qty_out;
            $item->save();

            InventoryMovement::create([
                'item_id' => $item->id,
                'movement_type' => 'sale',
                'quantity' => -1 * $line->inventory_qty_out,
                'unit_cost' => $item->cost_price ?? $item->costo,
                'related_type' => Invoice::class,
                'related_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'occurred_at' => now(),
                'meta' => null,
            ]);
        }
    }

    private function billingDefaults(): array
    {
        $settings = BillingSetting::query()->latest('id')->first();

        return [
            'pos_prefix' => $settings?->pos_prefix ?? config('billing.pos_prefix'),
            'default_tax_rate' => (float) ($settings?->default_tax_rate ?? config('billing.default_tax_rate')),
            'default_commission_rate' => (float) ($settings?->default_commission_rate ?? config('billing.default_commission_rate')),
            'currency' => $settings?->currency ?? config('billing.currency'),
        ];
    }

    private function nextNumber(?string $prefix): int
    {
        $last = Invoice::query()
            ->when($prefix, fn ($query) => $query->where('prefix', $prefix))
            ->orderByDesc('number')
            ->lockForUpdate()
            ->first();

        return ($last?->number ?? 0) + 1;
    }

    private function normalizeRate(float|int|string $rate): float
    {
        $value = (float) $rate;

        return $value > 1 ? $value / 100 : $value;
    }
}
