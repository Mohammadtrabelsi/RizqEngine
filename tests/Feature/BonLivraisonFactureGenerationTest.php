<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\BonLivraison;
use App\Models\Commande;
use App\Models\CommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\BonLivraisonService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonLivraisonFactureGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function makeBonLivraison(Customer $customer, Product $product): BonLivraison
    {
        $commande = Commande::create([
            'date' => '2026-05-01',
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

        return app(BonLivraisonService::class)->createFromCommande($commande->refresh());
    }

    /** @test */
    public function it_generates_a_facture_from_a_bon_livraison_preserving_all_business_data(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $bonLivraison = $this->makeBonLivraison($customer, $product);

        $sale = app(SaleService::class)->createFactureFromBonLivraison($bonLivraison);

        // Traceability to both the delivery note and its order.
        $this->assertSame($bonLivraison->id, $sale->bon_livraison_id);
        $this->assertSame($bonLivraison->commande_id, $sale->commande_id);
        $this->assertSame($sale->id, $bonLivraison->fresh()->sale->id);

        // Source delivery note and order both marked invoiced.
        $this->assertSame(BonLivraison::STATUS_INVOICED, $bonLivraison->fresh()->status);
        $this->assertSame(Commande::STATUS_INVOICED, $bonLivraison->commande->fresh()->status);

        // Amounts preserved exactly.
        foreach (['tax_percentage', 'discount_percentage', 'tax_amount', 'discount_amount', 'shipping_amount', 'total_amount'] as $column) {
            $this->assertSame($bonLivraison->getRawOriginal($column), $sale->getRawOriginal($column), "column {$column} preserved");
        }

        // Default facture is Pending => no stock movement on generation.
        $this->assertSame('Pending', $sale->status);
        $this->assertEquals(50, $product->fresh()->product_quantity);
    }

    /** @test */
    public function generating_a_facture_from_a_bon_livraison_twice_is_rejected(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $bonLivraison = $this->makeBonLivraison($customer, $product);

        app(SaleService::class)->createFactureFromBonLivraison($bonLivraison);

        try {
            app(SaleService::class)->createFactureFromBonLivraison($bonLivraison->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, Sale::where('bon_livraison_id', $bonLivraison->id)->count());
    }

    /** @test */
    public function invoicing_the_order_directly_blocks_a_second_facture_via_the_delivery_note(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $bonLivraison = $this->makeBonLivraison($customer, $product);

        // Invoice the underlying Commande first (the other path).
        app(SaleService::class)->createFactureFromCommande($bonLivraison->commande);

        try {
            app(SaleService::class)->createFactureFromBonLivraison($bonLivraison->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, Sale::where('commande_id', $bonLivraison->commande_id)->count());
    }

    /** @test */
    public function generating_a_completed_facture_from_a_bon_livraison_deducts_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $bonLivraison = $this->makeBonLivraison($customer, $product);

        $sale = app(SaleService::class)->createFactureFromBonLivraison($bonLivraison, ['status' => 'Completed']);

        $this->assertSame('Completed', $sale->status);
        $this->assertEquals(46, $product->fresh()->product_quantity);
    }

    /** @test */
    public function the_generate_facture_route_requires_the_permission(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $bonLivraison = $this->makeBonLivraison($customer, $product);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('bon-livraisons.convert', $bonLivraison))
            ->assertForbidden();

        $this->assertSame(0, Sale::count());
    }

    /** @test */
    public function the_generate_facture_route_creates_a_sale_for_an_authorized_user(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $bonLivraison = $this->makeBonLivraison($customer, $product);

        $user = User::factory()->create();
        $user->givePermissionTo('convert_bon_livraisons');

        $this->actingAs($user)
            ->post(route('bon-livraisons.convert', $bonLivraison))
            ->assertRedirect();

        $this->assertSame(1, Sale::where('bon_livraison_id', $bonLivraison->id)->count());
    }
}
