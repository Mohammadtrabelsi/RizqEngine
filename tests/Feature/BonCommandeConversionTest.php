<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\BonCommande;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetails;
use App\Models\User;
use App\Services\BonCommandeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonCommandeConversionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a Devis (Quotation) with one line, storing amounts as integer cents
     * exactly the way the QuotationController does.
     */
    private function makeQuotation(Customer $customer, Product $product): Quotation
    {
        $quotation = Quotation::create([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 10,
            'discount_percentage' => 5,
            'shipping_amount' => 15 * 100,
            'total_amount' => 230 * 100,
            'status' => 'Sent',
            'note' => 'Original devis note',
            'tax_amount' => 20 * 100,
            'discount_amount' => 5 * 100,
        ]);

        QuotationDetails::create([
            'quotation_id' => $quotation->id,
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

        return $quotation->refresh();
    }

    /** @test */
    public function it_converts_a_quotation_into_a_bon_commande_preserving_all_business_data(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        $bonCommande = app(BonCommandeService::class)->createFromQuotation($quotation);

        // Relationship / traceability.
        $this->assertSame($quotation->id, $bonCommande->quotation_id);
        $this->assertSame($bonCommande->id, $quotation->fresh()->bonCommande->id);
        $this->assertStringStartsWith('BC-', $bonCommande->reference);
        $this->assertSame(BonCommande::STATUS_DRAFT, $bonCommande->status);

        // Customer preserved.
        $this->assertSame($customer->id, $bonCommande->customer_id);
        $this->assertSame($customer->customer_name, $bonCommande->customer_name);

        // Header amounts preserved exactly (raw integer cents copied across).
        $this->assertSame($quotation->getRawOriginal('tax_percentage'), $bonCommande->getRawOriginal('tax_percentage'));
        $this->assertSame($quotation->getRawOriginal('discount_percentage'), $bonCommande->getRawOriginal('discount_percentage'));
        $this->assertSame($quotation->getRawOriginal('tax_amount'), $bonCommande->getRawOriginal('tax_amount'));
        $this->assertSame($quotation->getRawOriginal('discount_amount'), $bonCommande->getRawOriginal('discount_amount'));
        $this->assertSame($quotation->getRawOriginal('shipping_amount'), $bonCommande->getRawOriginal('shipping_amount'));
        $this->assertSame($quotation->getRawOriginal('total_amount'), $bonCommande->getRawOriginal('total_amount'));
        $this->assertSame('Original devis note', $bonCommande->note);

        // Line items preserved: quantities, prices, discounts, taxes, totals.
        $this->assertCount(1, $bonCommande->bonCommandeDetails);
        $qDetail = $quotation->quotationDetails->first();
        $bDetail = $bonCommande->bonCommandeDetails->first();

        $this->assertSame($product->id, $bDetail->product_id);
        $this->assertSame('Widget', $bDetail->product_name);
        $this->assertSame(4, $bDetail->quantity);
        $this->assertSame($qDetail->getRawOriginal('price'), $bDetail->getRawOriginal('price'));
        $this->assertSame($qDetail->getRawOriginal('unit_price'), $bDetail->getRawOriginal('unit_price'));
        $this->assertSame($qDetail->getRawOriginal('sub_total'), $bDetail->getRawOriginal('sub_total'));
        $this->assertSame($qDetail->getRawOriginal('product_discount_amount'), $bDetail->getRawOriginal('product_discount_amount'));
        $this->assertSame('fixed', $bDetail->product_discount_type);
        $this->assertSame($qDetail->getRawOriginal('product_tax_amount'), $bDetail->getRawOriginal('product_tax_amount'));

        // The original Devis remains untouched / traceable.
        $this->assertDatabaseHas('quotations', ['id' => $quotation->id]);
    }

    /** @test */
    public function converting_the_same_quotation_twice_is_rejected(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        app(BonCommandeService::class)->createFromQuotation($quotation);

        $this->expectException(ConversionException::class);
        app(BonCommandeService::class)->createFromQuotation($quotation->fresh());
    }

    /** @test */
    public function a_second_conversion_does_not_create_a_duplicate_bon_commande(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        app(BonCommandeService::class)->createFromQuotation($quotation);

        try {
            app(BonCommandeService::class)->createFromQuotation($quotation->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, BonCommande::where('quotation_id', $quotation->id)->count());
    }

    /** @test */
    public function the_convert_route_requires_the_permission(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('quotations.convert', $quotation))
            ->assertForbidden();

        $this->assertSame(0, BonCommande::count());
    }

    /** @test */
    public function the_convert_route_creates_a_bon_commande_for_an_authorized_user(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        $user = User::factory()->create();
        $user->givePermissionTo('convert_quotations');

        $this->actingAs($user)
            ->post(route('quotations.convert', $quotation))
            ->assertRedirect();

        $this->assertSame(1, BonCommande::where('quotation_id', $quotation->id)->count());
    }
}
