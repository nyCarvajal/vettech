<?php

namespace Tests\Unit;

use App\Services\Pricing\TaxCalculator;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    public function test_calcula_totales_por_linea(): void
    {
        $calculator = new TaxCalculator();
        $result = $calculator->calculateLine(2, 1000, 0.1, 0.19, 0.05);

        $this->assertEquals(2000, $result['line_subtotal']);
        $this->assertEquals(200, $result['discount_amount']);
        $this->assertEquals(342, $result['tax_amount']);
        $this->assertEquals(90, $result['commission_amount']);
        $this->assertEquals(2232, $result['line_total']);
    }
}
