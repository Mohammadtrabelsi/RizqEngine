<?php

namespace Tests\Feature;

use App\Exceptions\ConversionException;
use App\Models\Commande;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetails;
use App\Models\User;
use App\Services\BonCommandeService;
use App\Services\CommandeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The shorter Devis → Commande (direct) path.
 */
class QuotationToCommandeConversionTest extends TestCase
{
    use RefreshDatabase;

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
    public function it_converts_a_quotation_directly_into_a_commande_preserving_all_business_data(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        $commande = app(CommandeService::class)->createFromQuotation($quotation);

        $this->assertSame($quotation->id, $commande->quotation_id);
        $this->assertSame($commande->id, $quotation->fresh()->commande->id);
        $this->assertNull($commande->bon_commande_id);
        $this->assertStringStartsWith('CMD-', $commande->reference);
        $this->assertSame(Commande::STATUS_PENDING, $commande->status);
        $this->assertTrue($quotation->fresh()->isConverted());

        foreach (['tax_percentage', 'discount_percentage', 'tax_amount', 'discount_amount', 'shipping_amount', 'total_amount'] as $column) {
            $this->assertSame($quotation->getRawOriginal($column), $commande->getRawOriginal($column), "column {$column} preserved");
        }

        $this->assertCount(1, $commande->commandeDetails);
        $qDetail = $quotation->quotationDetails->first();
        $cDetail = $commande->commandeDetails->first();
        foreach (['price', 'unit_price', 'sub_total', 'product_discount_amount', 'product_tax_amount'] as $column) {
            $this->assertSame($qDetail->getRawOriginal($column), $cDetail->getRawOriginal($column), "line {$column} preserved");
        }
    }

    /** @test */
    public function a_quotation_already_turned_into_a_bon_commande_cannot_be_converted_directly(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        app(BonCommandeService::class)->createFromQuotation($quotation);

        $this->expectException(ConversionException::class);
        app(CommandeService::class)->createFromQuotation($quotation->fresh());
    }

    /** @test */
    public function converting_the_same_quotation_directly_twice_is_rejected_and_creates_no_duplicate(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        app(CommandeService::class)->createFromQuotation($quotation);

        try {
            app(CommandeService::class)->createFromQuotation($quotation->fresh());
        } catch (ConversionException) {
            // expected
        }

        $this->assertSame(1, Commande::where('quotation_id', $quotation->id)->count());
    }

    /** @test */
    public function the_direct_convert_route_requires_the_permission(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('quotations.convert-commande', $quotation))
            ->assertForbidden();

        $this->assertSame(0, Commande::count());
    }

    /** @test */
    public function the_direct_convert_route_creates_a_commande_for_an_authorized_user(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $quotation = $this->makeQuotation($customer, $product);

        $user = User::factory()->create();
        $user->givePermissionTo('convert_quotations_to_commande');

        $this->actingAs($user)
            ->post(route('quotations.convert-commande', $quotation))
            ->assertRedirect();

        $this->assertSame(1, Commande::where('quotation_id', $quotation->id)->count());
    }
}
