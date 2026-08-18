<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\Commande;
use App\Models\CommandeDetails;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureGenerationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a confirmed Commande with one line (integer cents).
     */
    private function makeConfirmedCommande(Customer $customer, Product $product): Commande
    {
        $commande = Commande::create([
            'date' => '2026-03-01',
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
    public function it_generates_a_facture_from_a_commande_preserving_all_business_data(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $commande = $this->makeConfirmedCommande($customer, $product);

        $sale = app(SaleService::class)->createFactureFromCommande($commande);

        // Relationship / traceability.
        $this->assertSame($commande->id, $sale->commande_id);
        $this->assertSame($sale->id, $commande->fresh()->sale->id);

        // Source Commande is now marked invoiced.
        $this->assertSame(Commande::STATUS_INVOICED, $commande->fresh()->status);

        // Customer preserved.
        $this->assertSame($customer->id, $sale->customer_id);
        $this->assertSame($customer->customer_name, $sale->customer_name);

        // Amounts and taxes preserved exactly.
        foreach (['tax_percentage', 'discount_percentage', 'tax_amount', 'discount_amount', 'shipping_amount', 'total_amount'] as $column) {
            $this->assertSame($commande->getRawOriginal($column), $sale->getRawOriginal($column), "column {$column} preserved");
        }

        // Line items preserved.
        $this->assertCount(1, $sale->saleDetails);
        $cDetail = $commande->commandeDetails->first();
        $sDetail = $sale->saleDetails->first();
        $this->assertSame($product->id, $sDetail->product_id);
        $this->assertSame(4, $sDetail->quantity);
        foreach (['price', 'unit_price', 'sub_total', 'product_discount_amount', 'product_tax_amount'] as $column) {
            $this->assertSame($cDetail->getRawOriginal($column), $sDetail->getRawOriginal($column), "line {$column} preserved");
        }

        // Default facture is a Pending sale => no stock movement on generation.
        $this->assertSame('Pending', $sale->status);
        $this->assertEquals(50, $product->fresh()->product_quantity);
    }

    /** @test */
    public function generating_a_facture_twice_is_rejected_and_creates_no_duplicate(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $commande = $this->makeConfirmedCommande($customer, $product);

        app(SaleService::class)->createFactureFromCommande($commande);

        try {
            app(SaleService::class)->createFactureFromCommande($commande->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, Sale::where('commande_id', $commande->id)->count());
    }

    /** @test */
    public function generating_a_completed_facture_deducts_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $commande = $this->makeConfirmedCommande($customer, $product);

        $sale = app(SaleService::class)->createFactureFromCommande($commande, ['status' => 'Completed']);

        $this->assertSame('Completed', $sale->status);
        $this->assertEquals(46, $product->fresh()->product_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 4,
            'reference_type' => 'Sale',
            'reference_id' => $sale->id,
        ]);
    }

    /** @test */
    public function the_generate_facture_route_requires_the_permission(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $commande = $this->makeConfirmedCommande($customer, $product);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('commandes.convert', $commande))
            ->assertForbidden();

        $this->assertSame(0, Sale::count());
    }

    /** @test */
    public function the_generate_facture_route_creates_a_sale_for_an_authorized_user(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['product_quantity' => 50]);
        $commande = $this->makeConfirmedCommande($customer, $product);

        $user = User::factory()->create();
        $user->givePermissionTo('convert_commandes');

        $this->actingAs($user)
            ->post(route('commandes.convert', $commande))
            ->assertRedirect();

        $this->assertSame(1, Sale::where('commande_id', $commande->id)->count());
    }
}
