<?php

namespace Tests\Feature;

use App\Livewire\CashRegister\CashRegisterIndex;
use App\Models\CashRegisterSession;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use App\Services\CashRegisterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CashRegisterTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    private function makeSale(): Sale
    {
        return Sale::create([
            'date' => now()->toDateString(),
            'customer_name' => 'Walk-in',
            'total_amount' => 0,
            'paid_amount' => 0,
            'due_amount' => 0,
            'status' => 'Completed',
            'payment_status' => 'Paid',
            'payment_method' => 'Cash',
        ]);
    }

    public function test_opening_a_session_records_the_float(): void
    {
        $user = User::factory()->create();
        $session = app(CashRegisterService::class)->open($user, 100_00);

        $this->assertSame(100_00, (int) $session->opening_float);
        $this->assertTrue($session->isOpen());
    }

    public function test_a_user_cannot_open_two_sessions(): void
    {
        $user = User::factory()->create();
        app(CashRegisterService::class)->open($user, 100_00);

        $this->expectException(\RuntimeException::class);
        app(CashRegisterService::class)->open($user, 50_00);
    }

    public function test_expected_cash_includes_cash_sales_only(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $session = app(CashRegisterService::class)->open($user, 100_00);
        $sale = $this->makeSale();

        // Cash payment counts; card payment does not.
        SalePayment::create(['sale_id' => $sale->id, 'amount' => 40, 'date' => now(), 'reference' => 'A', 'payment_method' => 'Cash']);
        SalePayment::create(['sale_id' => $sale->id, 'amount' => 25, 'date' => now(), 'reference' => 'B', 'payment_method' => 'Card']);

        $this->assertSame(40_00, app(CashRegisterService::class)->cashSales($session));
        $this->assertSame(140_00, app(CashRegisterService::class)->expectedCash($session));
    }

    public function test_closing_records_expected_counted_and_difference(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $service = app(CashRegisterService::class);
        $session = $service->open($user, 100_00);
        $sale = $this->makeSale();
        SalePayment::create(['sale_id' => $sale->id, 'amount' => 50, 'date' => now(), 'reference' => 'A', 'payment_method' => 'cash']);

        // Expected = 150.00; cashier counts 148.00 -> shortfall of 2.00.
        $closed = $service->close($session, 148_00);

        $this->assertSame(150_00, (int) $closed->expected_amount);
        $this->assertSame(148_00, (int) $closed->closing_amount);
        $this->assertSame(-2_00, (int) $closed->difference);
        $this->assertFalse($closed->isOpen());
    }

    public function test_a_closed_session_cannot_be_closed_again(): void
    {
        $session = CashRegisterSession::factory()->closed()->create();

        $this->expectException(\RuntimeException::class);
        app(CashRegisterService::class)->close($session, 100_00);
    }

    public function test_z_report_shape(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $service = app(CashRegisterService::class);
        $session = $service->open($user, 100_00);
        $closed = $service->close($session, 100_00);

        $report = $service->zReport($closed);

        $this->assertEqualsCanonicalizing(
            ['opening_float', 'cash_sales', 'expected', 'counted', 'difference'],
            array_keys($report)
        );
        $this->assertSame(0, $report['difference']);
    }

    public function test_livewire_open_and_close_flow(): void
    {
        $user = $this->userWith(['access_cash_register', 'open_cash_register', 'close_cash_register']);

        Livewire::actingAs($user)
            ->test(CashRegisterIndex::class)
            ->set('opening_float', 100)
            ->call('open');

        $this->assertDatabaseHas('cash_register_sessions', [
            'user_id' => $user->id,
            'opening_float' => 100_00,
            'status' => 'open',
        ]);

        Livewire::actingAs($user)
            ->test(CashRegisterIndex::class)
            ->set('counted_amount', 100)
            ->call('close');

        $this->assertDatabaseHas('cash_register_sessions', [
            'user_id' => $user->id,
            'closing_amount' => 100_00,
            'status' => 'closed',
        ]);
    }

    public function test_opening_is_blocked_without_permission(): void
    {
        $user = $this->userWith(['access_cash_register']);

        Livewire::actingAs($user)
            ->test(CashRegisterIndex::class)
            ->set('opening_float', 50)
            ->call('open')
            ->assertStatus(403);
    }
}
