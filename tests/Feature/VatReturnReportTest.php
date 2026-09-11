<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Services\Reports\VatReturnService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VatReturnReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Amounts are stored in integer cents, so a TTC of 1190 with 190 of VAT is
     * persisted as 119000 / 19000.
     */
    private function makeSale(Customer $customer, int $totalCents, int $vatCents, int $rate, string $date): void
    {
        Sale::create([
            'date' => $date,
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => $rate,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => $totalCents,
            'due_amount' => $totalCents,
            'status' => 'Completed',
            'payment_status' => 'Unpaid',
            'payment_method' => 'Cash',
            'tax_amount' => $vatCents,
            'discount_amount' => 0,
        ]);
    }

    private function makePurchase(Supplier $supplier, int $totalCents, int $vatCents, int $rate, string $date): void
    {
        Purchase::create([
            'date' => $date,
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->supplier_name,
            'tax_percentage' => $rate,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => $totalCents,
            'due_amount' => $totalCents,
            'status' => 'Completed',
            'payment_status' => 'Unpaid',
            'payment_method' => 'Cash',
            'tax_amount' => $vatCents,
            'discount_amount' => 0,
        ]);
    }

    public function test_vat_due_is_collected_minus_deductible(): void
    {
        $customer = Customer::factory()->create();
        $supplier = Supplier::factory()->create();

        // Collected: TTC 1190, VAT 190 @ 19%.
        $this->makeSale($customer, 119000, 19000, 19, '2026-01-15');
        // Deductible: TTC 595, VAT 95 @ 19%.
        $this->makePurchase($supplier, 59500, 9500, 19, '2026-01-20');

        $report = app(VatReturnService::class)->build('2026-01-01', '2026-01-31');

        $this->assertEqualsWithDelta(190.0, $report['collected_total'], 0.001);
        $this->assertEqualsWithDelta(95.0, $report['deductible_total'], 0.001);
        $this->assertEqualsWithDelta(95.0, $report['vat_due'], 0.001);
        $this->assertFalse($report['is_credit']);

        $this->assertCount(1, $report['collected']);
        $this->assertEqualsWithDelta(1000.0, $report['collected'][0]['base'], 0.001);
        $this->assertSame(19.0, $report['collected'][0]['rate']);
    }

    public function test_excess_deductible_vat_is_reported_as_a_credit(): void
    {
        $customer = Customer::factory()->create();
        $supplier = Supplier::factory()->create();

        $this->makeSale($customer, 59500, 9500, 19, '2026-02-10');
        $this->makePurchase($supplier, 119000, 19000, 19, '2026-02-12');

        $report = app(VatReturnService::class)->build('2026-02-01', '2026-02-28');

        $this->assertEqualsWithDelta(-95.0, $report['vat_due'], 0.001);
        $this->assertTrue($report['is_credit']);
    }

    public function test_documents_outside_the_period_are_excluded(): void
    {
        $customer = Customer::factory()->create();

        $this->makeSale($customer, 119000, 19000, 19, '2026-03-15');

        $report = app(VatReturnService::class)->build('2026-01-01', '2026-01-31');

        $this->assertSame(0.0, $report['collected_total']);
        $this->assertCount(0, $report['collected']);
    }
}
