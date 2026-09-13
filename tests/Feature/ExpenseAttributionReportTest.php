<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Vehicle;
use App\Services\Reports\ExpenseByDriverReportService;
use App\Services\Reports\ExpenseByVehicleReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseAttributionReportTest extends TestCase
{
    use RefreshDatabase;

    private function makeExpense(float $amount, string $date, array $attributes = []): void
    {
        Expense::create(array_merge([
            'category_id' => ExpenseCategory::factory()->create()->id,
            'date' => $date,
            'amount' => $amount,
            'details' => 'test',
        ], $attributes));
    }

    public function test_vehicle_report_groups_and_totals_expenses_per_vehicle(): void
    {
        $a = Vehicle::factory()->create(['registration' => 'AA-111']);
        $b = Vehicle::factory()->create(['registration' => 'BB-222']);

        $this->makeExpense(100, '2026-01-05', ['vehicle_id' => $a->id]);
        $this->makeExpense(50, '2026-01-10', ['vehicle_id' => $a->id]);
        $this->makeExpense(30, '2026-01-12', ['vehicle_id' => $b->id]);
        // Unattributed expense must be ignored by the report.
        $this->makeExpense(999, '2026-01-15');

        $rows = app(ExpenseByVehicleReportService::class)->rows('2026-01-01', '2026-01-31');
        $summary = app(ExpenseByVehicleReportService::class)->summary('2026-01-01', '2026-01-31');

        $this->assertCount(2, $rows);
        // Ordered by total descending: vehicle A (150) first.
        $this->assertSame($a->id, $rows[0]->vehicle_id);
        $this->assertEqualsWithDelta(150.0, $rows[0]->total_amount, 0.001);
        $this->assertSame(2, $rows[0]->count);

        $this->assertSame(3, $summary['count']);
        $this->assertEqualsWithDelta(180.0, $summary['total_amount'], 0.001);
    }

    public function test_driver_report_groups_and_totals_expenses_per_driver(): void
    {
        $driver = Driver::factory()->create(['name' => 'Ali']);

        $this->makeExpense(40, '2026-02-05', ['driver_id' => $driver->id]);
        $this->makeExpense(60, '2026-02-06', ['driver_id' => $driver->id]);
        // Outside the period.
        $this->makeExpense(500, '2026-03-01', ['driver_id' => $driver->id]);

        $rows = app(ExpenseByDriverReportService::class)->rows('2026-02-01', '2026-02-28');
        $summary = app(ExpenseByDriverReportService::class)->summary('2026-02-01', '2026-02-28');

        $this->assertCount(1, $rows);
        $this->assertSame('Ali', $rows[0]->name);
        $this->assertSame(2, $rows[0]->count);
        $this->assertEqualsWithDelta(100.0, $rows[0]->total_amount, 0.001);

        $this->assertSame(2, $summary['count']);
        $this->assertEqualsWithDelta(100.0, $summary['total_amount'], 0.001);
    }
}
