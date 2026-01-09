<?php

namespace App\Services;

use App\Models\CashClosure;
use App\Models\CashMovement;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CashClosureService
{
    public function getSummary(string $date, ?int $clinicId = null): array
    {
        $day = Carbon::parse($date);
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        $payments = InvoicePayment::with(['invoice.owner'])
            ->whereBetween('paid_at', [$start, $end])
            ->get();

        $expectedByMethod = $payments->groupBy('method')
            ->map(fn (Collection $items) => $items->sum('amount'));

        $expectedCash = (float) ($expectedByMethod->get('cash', 0));
        $expectedCard = (float) ($expectedByMethod->get('card', 0));
        $expectedTransfer = (float) ($expectedByMethod->get('transfer', 0));
        $totalExpected = (float) $expectedByMethod->sum();

        $paymentDetails = $payments->map(function (InvoicePayment $payment) {
            return [
                'time' => $payment->paid_at?->format('H:i'),
                'client' => $payment->invoice?->owner?->name ?? 'Sin cliente',
                'invoice' => $payment->invoice?->full_number ?? $payment->invoice_id,
                'method' => $payment->method,
                'amount' => (float) $payment->amount,
            ];
        });

        $paymentCount = $payments->count();
        $invoiceCount = $payments->pluck('invoice_id')->unique()->count();

        $expensesAvailable = $this->hasCashMovementTable();
        $expenses = collect();
        $expensesByMethod = collect();
        $expensesTotal = 0.0;

        if ($expensesAvailable) {
            $expenses = CashMovement::where('type', 'expense')
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $expensesByMethod = $expenses->groupBy('method')
                ->map(fn (Collection $items) => $items->sum('amount'));

            $expensesTotal = (float) $expenses->sum('amount');
        }

        $expenseDetails = $expenses->map(function (CashMovement $expense) {
            return [
                'time' => $expense->created_at?->format('H:i'),
                'category' => $expense->reason,
                'description' => $expense->reason,
                'method' => $expense->method,
                'amount' => (float) $expense->amount,
            ];
        });

        $net = $totalExpected - $expensesTotal;

        return [
            'date' => $day->toDateString(),
            'expected' => [
                'cash' => $expectedCash,
                'card' => $expectedCard,
                'transfer' => $expectedTransfer,
                'total' => $totalExpected,
            ],
            'payments' => $paymentDetails,
            'payment_counts' => [
                'payments' => $paymentCount,
                'invoices' => $invoiceCount,
            ],
            'expenses' => [
                'available' => $expensesAvailable,
                'total' => $expensesTotal,
                'by_method' => $expensesByMethod->all(),
                'items' => $expenseDetails,
            ],
            'net' => $net,
            'expected_by_method' => $expectedByMethod->all(),
        ];
    }

    public function storeClosure(array $data, array $summary, int $userId, ?int $clinicId = null): CashClosure
    {
        $expected = $summary['expected'];

        $countedCash = (float) $data['counted_cash'];
        $countedCard = (float) ($data['counted_card'] ?? 0);
        $countedTransfer = (float) ($data['counted_transfer'] ?? 0);

        $totalCounted = $countedCash + $countedCard + $countedTransfer;
        $totalExpected = (float) $expected['total'];
        $difference = $totalCounted - $totalExpected;

        return CashClosure::updateOrCreate(
            ['clinica_id' => $clinicId, 'date' => $summary['date']],
            [
                'user_id' => $userId,
                'status' => 'closed',
                'expected_cash' => $expected['cash'],
                'counted_cash' => $countedCash,
                'difference' => $difference,
                'expected_card' => $expected['card'],
                'counted_card' => $countedCard,
                'expected_transfer' => $expected['transfer'],
                'counted_transfer' => $countedTransfer,
                'total_expected' => $totalExpected,
                'total_counted' => $totalCounted,
                'notes' => $data['notes'] ?? null,
                'meta' => [
                    'expected_by_method' => $summary['expected_by_method'],
                    'expenses' => $summary['expenses'],
                    'net' => $summary['net'],
                ],
            ]
        );
    }

    private function hasCashMovementTable(): bool
    {
        $connection = (new CashMovement())->getConnectionName();

        return Schema::connection($connection)->hasTable('cash_movements');
    }
}
