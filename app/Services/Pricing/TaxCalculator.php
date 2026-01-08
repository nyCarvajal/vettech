<?php

namespace App\Services\Pricing;

class TaxCalculator
{
    public function calculateLine(
        float $quantity,
        float $unitPrice,
        float $discountRate,
        float $taxRate,
        float $commissionRate
    ): array {
        $lineSubtotal = $quantity * $unitPrice;
        $discountAmount = $lineSubtotal * $discountRate;
        $base = $lineSubtotal - $discountAmount;
        $taxAmount = $base * $taxRate;
        $commissionAmount = $base * $commissionRate;
        $lineTotal = $base + $taxAmount + $commissionAmount;

        return [
            'line_subtotal' => round($lineSubtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'commission_amount' => round($commissionAmount, 2),
            'line_total' => round($lineTotal, 2),
        ];
    }
}
