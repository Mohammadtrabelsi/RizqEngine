<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\BonLivraison;
use App\Models\Commande;
use App\Models\CommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\BonLivraisonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonLivraisonConversionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a confirmed Commande with one line (integer cents).
     */
    private function makeConfirmedCommande(Customer $customer, Product $product): Commande
    {
        $commande = Commande::create([
            'date' => '2026-04-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 10,
            'discount_percentage' => 5,
            'shipping_amount' => 15 * 100,
            'total_amount' => 230 * 100,
            'status' => Commande::STATUS_CONFIRMED,
            'note' => 'CMD note',
            'tax_amount' => 20 * 100,
            'discount_amount' => 5 * 100,
        ]);

        CommandeDetails::create([
            'commande_id' => $commande->id,
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

        return $commande->refresh();
    }

    /** @test */
    public function it_converts_a_commande_into_a_bon_livraison_preserving_all_business_data(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);

        $bonLivraison = app(BonLivraisonService::class)->createFromCommande($commande);

        // Relationship / traceability both directions.
        $this->assertSame($commande->id, $bonLivraison->commande_id);
        $this->assertSame($bonLivraison->id, $commande->fresh()->bonLivraison->id);
        $this->assertStringStartsWith('BL-', $bonLivraison->reference);
        $this->assertSame(BonLivraison::STATUS_PENDING, $bonLivraison->status);

        // A single full conversion ships the whole order.
        $this->assertSame(Commande::SHIPPING_SHIPPED, $commande->fresh()->shipping_status);

        // Customer preserved.
        $this->assertSame($customer->id, $bonLivraison->customer_id);
        $this->assertSame($customer->customer_name, $bonLivraison->customer_name);

        // Header amounts preserved exactly.
        foreach (['tax_percentage', 'discount_percentage', 'tax_amount', 'discount_amount', 'shipping_amount', 'total_amount'] as $column) {
            $this->assertSame($commande->getRawOriginal($column), $bonLivraison->getRawOriginal($column), "column {$column} preserved");
        }
        $this->assertSame('CMD note', $bonLivraison->note);

        // Line items preserved.
        $this->assertCount(1, $bonLivraison->bonLivraisonDetails);
        $cDetail = $commande->commandeDetails->first();
        $bDetail = $bonLivraison->bonLivraisonDetails->first();
        $this->assertSame($product->id, $bDetail->product_id);
        $this->assertSame(4, $bDetail->quantity);
        foreach (['price', 'unit_price', 'sub_total', 'product_discount_amount', 'product_tax_amount'] as $column) {
            $this->assertSame($cDetail->getRawOriginal($column), $bDetail->getRawOriginal($column), "line {$column} preserved");
        }
        $this->assertSame('fixed', $bDetail->product_discount_type);
    }

    /** @test */
    public function converting_the_same_commande_twice_is_rejected_and_creates_no_duplicate(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);

        app(BonLivraisonService::class)->createFromCommande($commande);

        try {
            app(BonLivraisonService::class)->createFromCommande($commande->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, BonLivraison::where('commande_id', $commande->id)->count());
    }

    /** @test */
    public function a_pending_bon_livraison_can_be_marked_delivered(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);

        $bonLivraison = app(BonLivraisonService::class)->createFromCommande($commande);
        app(BonLivraisonService::class)->markDelivered($bonLivraison);

        $this->assertSame(BonLivraison::STATUS_DELIVERED, $bonLivraison->fresh()->status);
    }

    /** @test */
    public function it_ships_only_the_requested_quantities_and_tracks_the_remainder(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);
        $detail = $commande->commandeDetails->first();

        // Ordered quantity is 4 — ship 1 now.
        $bl = app(BonLivraisonService::class)->createFromCommande($commande, [$detail->id => 1]);

        $line = $bl->bonLivraisonDetails->first();
        $this->assertSame(1, $line->quantity);
        $this->assertSame($detail->id, $line->commande_detail_id);

        $commande->refresh();
        $this->assertSame(1, $commande->deliveredQuantities()[$detail->id]);
        $this->assertSame(3, $commande->remainingQuantities()[$detail->id]);
        $this->assertTrue($commande->isPartiallyDelivered());
        $this->assertFalse($commande->isFullyDelivered());
        $this->assertSame(Commande::SHIPPING_PARTIALLY_SHIPPED, $commande->shipping_status);

        // Line amounts are prorated (1 of 4 of sub_total 220_00 = 55_00).
        $this->assertSame(55 * 100, $line->getRawOriginal('sub_total'));
    }

    /** @test */
    public function successive_partial_shipments_reconcile_exactly_with_the_commande(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);
        $detail = $commande->commandeDetails->first();

        $service = app(BonLivraisonService::class);
        $service->createFromCommande($commande, [$detail->id => 1]);
        $service->createFromCommande($commande->fresh(), [$detail->id => 3]);

        $commande->refresh();
        $this->assertTrue($commande->isFullyDelivered());
        $this->assertSame(Commande::SHIPPING_SHIPPED, $commande->shipping_status);
        $this->assertSame(2, BonLivraison::where('commande_id', $commande->id)->count());

        // Quantities and every monetary column reconcile to the ordered totals.
        $this->assertSame(4, (int) BonLivraison::where('commande_id', $commande->id)
            ->join('bon_livraison_details', 'bon_livraisons.id', '=', 'bon_livraison_details.bon_livraison_id')
            ->sum('bon_livraison_details.quantity'));

        foreach (['tax_amount', 'discount_amount', 'shipping_amount', 'total_amount'] as $column) {
            $this->assertSame(
                (int) $commande->getRawOriginal($column),
                (int) BonLivraison::where('commande_id', $commande->id)->sum($column),
                "header {$column} reconciles"
            );
        }
    }

    /** @test */
    public function it_rejects_shipping_more_than_the_remaining_quantity(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);
        $detail = $commande->commandeDetails->first();

        $this->expectException(ConversionException::class);

        app(BonLivraisonService::class)->createFromCommande($commande, [$detail->id => 5]);
    }

    /** @test */
    public function the_convert_route_accepts_partial_quantities(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);
        $detail = $commande->commandeDetails->first();

        $user = User::factory()->create();
        $user->givePermissionTo('convert_commandes_to_bon_livraison');

        $this->actingAs($user)
            ->post(route('commandes.convert-bon-livraison', $commande), [
                'quantities' => [$detail->id => 2],
            ])
            ->assertRedirect();

        $bl = BonLivraison::where('commande_id', $commande->id)->firstOrFail();
        $this->assertSame(2, $bl->bonLivraisonDetails->first()->quantity);
        $this->assertSame(2, $commande->fresh()->remainingQuantities()[$detail->id]);
    }

    /** @test */
    public function the_convert_route_requires_the_permission(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('commandes.convert-bon-livraison', $commande))
            ->assertForbidden();

        $this->assertSame(0, BonLivraison::count());
    }

    /** @test */
    public function the_convert_route_creates_a_bon_livraison_for_an_authorized_user(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $commande = $this->makeConfirmedCommande($customer, $product);

        $user = User::factory()->create();
        $user->givePermissionTo('convert_commandes_to_bon_livraison');

        $this->actingAs($user)
            ->post(route('commandes.convert-bon-livraison', $commande))
            ->assertRedirect();

        $this->assertSame(1, BonLivraison::where('commande_id', $commande->id)->count());
    }
}
