<?php

namespace Tests\Unit;

use App\Enums\WithholdingCalculationBase;
use App\Services\WithholdingTaxCalculator;
use PHPUnit\Framework\TestCase;

class WithholdingTaxCalculatorTest extends TestCase
{
    private WithholdingTaxCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new WithholdingTaxCalculator;
    }

    /**
     * A lightweight stand-in for a WithholdingTax, so the pure calculator can
     * be tested without booting the framework or hitting the database.
     */
    private function withholding(float $rate, string $base, int $id = 1): object
    {
        return (object) [
            'id' => $id,
            'name' => 'RAS '.$rate,
            'code' => 'RAS'.$id,
            'rate' => $rate,
            'calculation_base' => $base,
        ];
    }

    /** @test — Test 1: 3% withholding on the TTC. */
    public function three_percent_on_ttc(): void
    {
        $result = $this->calculator->calculate(
            [$this->withholding(3, WithholdingCalculationBase::TTC->value)],
            1000.0, 190.0, 1190.0,
        );

        $this->assertSame(35.700, $result['total']);
        $this->assertSame(1154.300, $result['net_payable']);
        $this->assertSame(1190.0, $result['lines'][0]['taxable_amount']);
    }

    /** @test — Test 2: 3% withholding on the HT. */
    public function three_percent_on_ht(): void
    {
        $result = $this->calculator->calculate(
            [$this->withholding(3, WithholdingCalculationBase::HT->value)],
            1000.0, 190.0, 1190.0,
        );

        $this->assertSame(30.0, $result['total']);
        $this->assertSame(1160.0, $result['net_payable']);
    }

    /** @test — Test 3: no withholding leaves the net equal to the TTC. */
    public function no_withholding_keeps_net_equal_to_ttc(): void
    {
        $result = $this->calculator->calculate([], 1000.0, 190.0, 1190.0);

        $this->assertSame(0.0, $result['total']);
        $this->assertSame(1190.0, $result['net_payable']);
        $this->assertSame([], $result['lines']);
    }

    /** @test — Test 4: several withholdings on the same document. */
    public function multiple_withholdings_sum_independently(): void
    {
        $result = $this->calculator->calculate(
            [
                $this->withholding(3, WithholdingCalculationBase::TTC->value, 1),
                $this->withholding(1, WithholdingCalculationBase::HT->value, 2),
            ],
            1000.0, 190.0, 1190.0,
        );

        // 3% of 1190 = 35.700 ; 1% of 1000 = 10.000 ; total 45.700
        $this->assertSame(45.700, $result['total']);
        $this->assertSame(1144.300, $result['net_payable']);
        $this->assertCount(2, $result['lines']);
    }

    /** @test — Test 7: rounding to three decimals (millimes). */
    public function rounds_to_three_decimals(): void
    {
        // 1.5% of 1234.567 = 18.5185... -> 18.519
        $result = $this->calculator->calculate(
            [$this->withholding(1.5, WithholdingCalculationBase::TTC->value)],
            1034.567, 200.0, 1234.567,
        );

        $this->assertSame(18.519, $result['lines'][0]['amount']);
        $this->assertSame(18.519, $result['total']);
        $this->assertSame(1216.048, $result['net_payable']);
    }
}
