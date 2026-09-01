<?php

namespace Tests\Feature;

use App\Models\FixedPayment;
use App\Models\MonthlyBudget;
use App\Models\Outing;
use App\Services\Finance\InvoiceArchiveService;
use App\Services\Finance\OutingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_outing_total_sums_every_category(): void
    {
        $outing = new Outing([
            'food' => 10,
            'gas' => 20,
            'water' => 5,
            'transport' => 15,
            'misc' => 2.5,
        ]);

        $this->assertSame(52.5, $outing->total());
    }

    public function test_remaining_balance_subtracts_fixed_and_outings(): void
    {
        $budget = MonthlyBudget::create([
            'year' => 2026,
            'month' => 8,
            'starting_budget' => 1000,
        ]);

        FixedPayment::create([
            'monthly_budget_id' => $budget->id,
            'label' => 'Rent',
            'category' => 'rent',
            'amount' => 300,
        ]);
        FixedPayment::create([
            'monthly_budget_id' => $budget->id,
            'label' => 'Internet',
            'category' => 'subscription',
            'amount' => 50,
        ]);

        // In-month outing (counts) and out-of-month outing (ignored).
        Outing::create([
            'reference' => 'BS-2026-00001',
            'date' => '2026-08-15',
            'food' => 100, 'gas' => 40, 'water' => 0, 'transport' => 10, 'misc' => 0,
        ]);
        Outing::create([
            'reference' => 'BS-2026-00002',
            'date' => '2026-09-01',
            'food' => 999, 'gas' => 0, 'water' => 0, 'transport' => 0, 'misc' => 0,
        ]);

        $this->assertSame(350.0, $budget->totalFixedPayments());
        $this->assertSame(150.0, $budget->totalOutings());
        $this->assertSame(500.0, $budget->totalExpenses());
        $this->assertSame(500.0, $budget->remainingBalance());
    }

    public function test_remaining_balance_can_go_negative(): void
    {
        $budget = MonthlyBudget::create([
            'year' => 2026, 'month' => 1, 'starting_budget' => 100,
        ]);
        FixedPayment::create([
            'monthly_budget_id' => $budget->id, 'label' => 'Loan', 'amount' => 250,
        ]);

        $this->assertSame(-150.0, $budget->remainingBalance());
    }

    public function test_next_reference_increments_per_year(): void
    {
        $service = new OutingService;

        Outing::create(['reference' => 'BS-2026-00001', 'date' => '2026-08-01', 'food' => 1]);
        Outing::create(['reference' => 'BS-2026-00002', 'date' => '2026-08-02', 'food' => 1]);

        $this->assertSame('BS-2026-00003', $service->nextReference(\Illuminate\Support\Carbon::parse('2026-08-03')));
        $this->assertSame('BS-2027-00001', $service->nextReference(\Illuminate\Support\Carbon::parse('2027-01-01')));
    }

    public function test_invoice_archive_filters_by_type_and_period(): void
    {
        $budget = MonthlyBudget::create(['year' => 2026, 'month' => 8, 'starting_budget' => 0]);
        FixedPayment::create([
            'monthly_budget_id' => $budget->id, 'label' => 'Rent', 'amount' => 300, 'due_date' => '2026-08-05',
        ]);
        Outing::create(['reference' => 'BS-2026-00001', 'date' => '2026-08-10', 'food' => 50]);
        Outing::create(['reference' => 'BS-2026-00002', 'date' => '2026-12-10', 'food' => 70]);

        $service = new InvoiceArchiveService;

        $all = $service->documents('2026-08-01', '2026-08-31', InvoiceArchiveService::TYPE_ALL);
        $this->assertCount(2, $all);

        $outingsOnly = $service->documents('2026-08-01', '2026-08-31', InvoiceArchiveService::TYPE_OUTINGS);
        $this->assertCount(1, $outingsOnly);
        $this->assertSame('outings', $outingsOnly->first()['type']);

        $fixedOnly = $service->documents(null, null, InvoiceArchiveService::TYPE_FIXED);
        $this->assertCount(1, $fixedOnly);

        $csv = $service->buildCsv('2026-08-01', '2026-08-31', InvoiceArchiveService::TYPE_ALL);
        $this->assertStringContainsString('BS-2026-00001', $csv);
        $this->assertStringContainsString('Rent', $csv);
    }
}
