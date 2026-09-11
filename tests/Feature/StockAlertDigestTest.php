<?php

namespace Tests\Feature;

use App\Mail\StockAlertDigest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StockAlertDigestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_a_digest_of_low_stock_and_expiring_products(): void
    {
        Mail::fake();

        User::factory()->create(['is_active' => 1, 'email' => 'manager@example.com']);

        // Low stock: quantity at/below the alert threshold.
        $low = Product::factory()->create([
            'product_quantity' => 2,
            'product_stock_alert' => 5,
            'expiry_date' => null,
        ]);

        // Expiring within the window and still holding stock.
        $expiring = Product::factory()->create([
            'product_quantity' => 20,
            'product_stock_alert' => 1,
            'expiry_date' => now()->addDays(10)->toDateString(),
        ]);

        // Healthy product that must not appear anywhere.
        $healthy = Product::factory()->create([
            'product_quantity' => 100,
            'product_stock_alert' => 5,
            'expiry_date' => now()->addYears(2)->toDateString(),
        ]);

        $this->artisan('stock:send-alerts', ['--days' => 30])
            ->assertSuccessful();

        Mail::assertSent(StockAlertDigest::class, function (StockAlertDigest $mail) use ($low, $expiring, $healthy) {
            return $mail->lowStock->contains('id', $low->id)
                && ! $mail->lowStock->contains('id', $healthy->id)
                && $mail->expiring->contains('id', $expiring->id)
                && ! $mail->expiring->contains('id', $healthy->id)
                && $mail->hasTo('manager@example.com');
        });
    }

    /** @test */
    public function it_sends_nothing_when_all_stock_is_healthy(): void
    {
        Mail::fake();

        User::factory()->create(['is_active' => 1]);

        Product::factory()->create([
            'product_quantity' => 100,
            'product_stock_alert' => 5,
            'expiry_date' => null,
        ]);

        $this->artisan('stock:send-alerts')->assertSuccessful();

        Mail::assertNothingSent();
    }

    /** @test */
    public function dry_run_reports_without_sending(): void
    {
        Mail::fake();

        User::factory()->create(['is_active' => 1]);

        Product::factory()->create([
            'product_quantity' => 0,
            'product_stock_alert' => 5,
            'expiry_date' => null,
        ]);

        $this->artisan('stock:send-alerts', ['--dry-run' => true])
            ->assertSuccessful();

        Mail::assertNothingSent();
    }
}
