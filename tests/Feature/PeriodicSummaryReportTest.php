<?php

namespace Tests\Feature;

use App\Livewire\Reports\PeriodicSummaryReport;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Outing;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Services\Reports\PeriodicSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PeriodicSummaryReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate('access_reports', 'web');
        $this->seedSettings();
    }

    /**
     * The shared currency helper reads the single settings row, so a currency +
     * settings record must exist for any component that renders money.
     */
    protected function seedSettings(): void
    {
        $currency = Currency::create([
            'currency_name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'exchange_rate' => 1,
        ]);

        Setting::create([
            'company_name' => 'Test Co',
            'company_email' => 'test@example.com',
            'company_phone' => '000',
            'default_currency_id' => $currency->id,
            'default_currency_position' => 'prefix',
            'notification_email' => 'test@example.com',
            'footer_text' => 'footer',
            'company_address' => 'address',
        ]);

        cache()->forget('settings');
    }

    /** Amounts on sales/purchases persist as integer cents ($100 => 10000). */
    protected function makeSale(string $date, int $totalCents, string $status = 'Completed'): Sale
    {
        return Sale::create([
            'date' => $date,
            'customer_name' => 'Walk-in',
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => $totalCents,
            'paid_amount' => $totalCents,
            'due_amount' => 0,
            'status' => $status,
            'payment_status' => 'Paid',
            'payment_method' => 'Cash',
        ]);
    }

    protected function makePurchase(string $date, int $totalCents, string $status = 'Completed'): Purchase
    {
        return Purchase::create([
            'date' => $date,
            'supplier_name' => 'Acme',
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => $totalCents,
            'paid_amount' => $totalCents,
            'due_amount' => 0,
            'status' => $status,
            'payment_status' => 'Paid',
            'payment_method' => 'Cash',
        ]);
    }

    protected function makeOuting(string $date, float $misc, string $ref): Outing
    {
        return Outing::create([
            'reference' => $ref,
            'date' => $date,
            'location' => 'Site',
            'purpose' => 'Trip',
            'food' => 0,
            'gas' => 0,
            'water' => 0,
            'transport' => 0,
            'misc' => $misc,
        ]);
    }

    protected function makeExpense(string $date, float $amount): Expense
    {
        $category = ExpenseCategory::factory()->create();

        return Expense::create([
            'category_id' => $category->id,
            'date' => $date,
            'details' => 'Charge',
            'amount' => $amount,
        ]);
    }

    /** @test */
    public function the_report_requires_the_reports_permission()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('periodic-summary-report.index'))
            ->assertForbidden();

        $user->givePermissionTo('access_reports');

        $this->actingAs($user)
            ->get(route('periodic-summary-report.index'))
            ->assertOk();
    }

    /** @test */
    public function it_aggregates_the_four_flows_per_day_and_computes_the_balance()
    {
        $today = Carbon::today()->toDateString();

        // Two completed sales same day => $150; one pending sale is excluded.
        $this->makeSale($today, 10000);
        $this->makeSale($today, 5000);
        $this->makeSale($today, 9999, 'Pending');

        $this->makePurchase($today, 4000);   // $40 achats
        $this->makeOuting($today, 10.0, 'BS-1'); // $10 sortie
        $this->makeExpense($today, 25.0);    // $25 charge

        $service = app(PeriodicSummaryService::class);
        $summary = $service->summary($today, $today, 'day');

        $this->assertCount(1, $summary['rows']);

        $row = $summary['rows'][0];
        $this->assertSame(150.0, $row['sales']);
        $this->assertSame(40.0, $row['purchases']);
        $this->assertSame(10.0, $row['outings']);
        $this->assertSame(25.0, $row['expenses']);
        // balance = 150 - (40 + 10 + 25) = 75
        $this->assertSame(75.0, $row['balance']);

        $this->assertSame(150.0, $summary['totals']['sales']);
        $this->assertSame(75.0, $summary['totals']['balance']);
    }

    /** @test */
    public function it_groups_sales_across_months_and_excludes_out_of_range_periods()
    {
        $thisMonth = Carbon::today()->startOfMonth()->toDateString();
        $lastMonth = Carbon::today()->startOfMonth()->subMonth()->toDateString();

        $this->makeSale($thisMonth, 10000);
        $this->makeSale($lastMonth, 20000);

        $service = app(PeriodicSummaryService::class);

        // Range covering both months, grouped monthly => two rows.
        $summary = $service->summary($lastMonth, $thisMonth, 'month');
        $this->assertCount(2, $summary['rows']);
        $this->assertSame(300.0, $summary['totals']['sales']);

        // Range restricted to this month only => the last-month row is excluded.
        $restricted = $service->summary($thisMonth, Carbon::today()->toDateString(), 'month');
        $this->assertCount(1, $restricted['rows']);
        $this->assertSame(100.0, $restricted['totals']['sales']);
    }

    /** @test */
    public function the_livewire_component_renders_the_summary()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('access_reports');

        $today = Carbon::today()->toDateString();
        $this->makeSale($today, 10000);

        Livewire::actingAs($user)
            ->test(PeriodicSummaryReport::class)
            ->set('start_date', $today)
            ->set('end_date', $today)
            ->set('grouping', 'day')
            ->call('generateReport')
            ->assertHasNoErrors()
            ->assertOk();
    }
}
