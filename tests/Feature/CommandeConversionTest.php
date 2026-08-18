<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\BonCommande;
use App\Models\BonCommandeDetails;
use App\Models\Commande;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\CommandeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeConversionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a confirmed Bon de Commande with one line (integer cents).
     */
    private function makeConfirmedBonCommande(Customer $customer, Product $product): BonCommande
    {
        $bonCommande = BonCommande::create([
            'date' => '2026-02-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 10,
            'discount_percentage' => 5,
            'shipping_amount' => 15 * 100,
            'total_amount' => 230 * 100,
            'status' => BonCommande::STATUS_CONFIRMED,
            'note' => 'BC note',
            'tax_amount' => 20 * 100,
            'discount_amount' => 5 * 100,
        ]);

        BonCommandeDetails::create([
            'bon_commande_id' => $bonCommande->id,
            'product_id' => $product->id,
            'product_name' => 'Widget',
            'product_code' => $product->product_code,
            'quantity' => 4,
            'price' => 55 * 100,
            'unit_price' => 50 * 100,
            'sub_total' => 220 * 100,
            'product_discount_amount' => 3 * 100,
            'product_discount_type' => 'fixed',
            'product_tax_amount' => 5 * 100,
        ]);

        return $bonCommande->refresh();
    }

    /** @test */
    public function it_converts_a_confirmed_bon_commande_into_a_commande_preserving_all_business_data(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $bonCommande = $this->makeConfirmedBonCommande($customer, $product);

        $commande = app(CommandeService::class)->createFromBonCommande($bonCommande);

        // Relationship / traceability both directions.
        $this->assertSame($bonCommande->id, $commande->bon_commande_id);
        $this->assertSame($commande->id, $bonCommande->fresh()->commande->id);
        $this->assertStringStartsWith('CMD-', $commande->reference);
        $this->assertSame(Commande::STATUS_PENDING, $commande->status);

        // Source BC is now marked converted (blocks a second conversion).
        $this->assertSame(BonCommande::STATUS_CONVERTED, $bonCommande->fresh()->status);

        // Customer preserved.
        $this->assertSame($customer->id, $commande->customer_id);
        $this->assertSame($customer->customer_name, $commande->customer_name);

        // Header amounts preserved exactly.
        foreach (['tax_percentage', 'discount_percentage', 'tax_amount', 'discount_amount', 'shipping_amount', 'total_amount'] as $column) {
            $this->assertSame($bonCommande->getRawOriginal($column), $commande->getRawOriginal($column), "column {$column} preserved");
        }
        $this->assertSame('BC note', $commande->note);

        // Line items preserved.
        $this->assertCount(1, $commande->commandeDetails);
        $bDetail = $bonCommande->bonCommandeDetails->first();
        $cDetail = $commande->commandeDetails->first();
        $this->assertSame($product->id, $cDetail->product_id);
        $this->assertSame(4, $cDetail->quantity);
        foreach (['price', 'unit_price', 'sub_total', 'product_discount_amount', 'product_tax_amount'] as $column) {
            $this->assertSame($bDetail->getRawOriginal($column), $cDetail->getRawOriginal($column), "line {$column} preserved");
        }
        $this->assertSame('fixed', $cDetail->product_discount_type);
    }

    /** @test */
    public function a_bon_commande_must_be_confirmed_before_conversion(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $bonCommande = $this->makeConfirmedBonCommande($customer, $product);
        $bonCommande->update(['status' => BonCommande::STATUS_DRAFT]);

        $this->expectException(ConversionException::class);
        app(CommandeService::class)->createFromBonCommande($bonCommande->fresh());
    }

    /** @test */
    public function converting_the_same_bon_commande_twice_is_rejected_and_creates_no_duplicate(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $bonCommande = $this->makeConfirmedBonCommande($customer, $product);

        app(CommandeService::class)->createFromBonCommande($bonCommande);

        try {
            // Force the status back so the guard under test is the unique link,
            // not the "converted" status.
            $bonCommande->update(['status' => BonCommande::STATUS_CONFIRMED]);
            app(CommandeService::class)->createFromBonCommande($bonCommande->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, Commande::where('bon_commande_id', $bonCommande->id)->count());
    }

    /** @test */
    public function the_convert_route_requires_the_permission(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $bonCommande = $this->makeConfirmedBonCommande($customer, $product);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('bon-commandes.convert', $bonCommande))
            ->assertForbidden();

        $this->assertSame(0, Commande::count());
    }

    /** @test */
    public function the_convert_route_creates_a_commande_for_an_authorized_user(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $bonCommande = $this->makeConfirmedBonCommande($customer, $product);

        $user = User::factory()->create();
        $user->givePermissionTo('convert_bon_commandes');

        $this->actingAs($user)
            ->post(route('bon-commandes.convert', $bonCommande))
            ->assertRedirect();

        $this->assertSame(1, Commande::where('bon_commande_id', $bonCommande->id)->count());
    }
}
