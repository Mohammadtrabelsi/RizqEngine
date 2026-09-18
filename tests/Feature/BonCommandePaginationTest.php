<?php

namespace Tests\Feature;

use App\Models\BonCommande;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\BonCommandeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonCommandePaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The index card list renders the linked Devis reference for each row, so the
     * quotation relation must be eager loaded. With lazy loading disabled (as in
     * AppServiceProvider) accessing it otherwise throws a
     * LazyLoadingViolationException.
     */
    public function test_paginate_eager_loads_the_quotation_relation(): void
    {
        Model::preventLazyLoading();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $quotation = Quotation::create([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 10,
            'discount_percentage' => 5,
            'shipping_amount' => 15 * 100,
            'total_amount' => 230 * 100,
            'status' => 'Sent',
            'note' => null,
            'tax_amount' => 20 * 100,
            'discount_amount' => 5 * 100,
        ]);

        BonCommande::create([
            'date' => '2026-01-02',
            'quotation_id' => $quotation->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 10,
            'discount_percentage' => 5,
            'shipping_amount' => 15 * 100,
            'total_amount' => 230 * 100,
            'status' => BonCommande::STATUS_DRAFT,
            'note' => null,
            'tax_amount' => 20 * 100,
            'discount_amount' => 5 * 100,
        ]);

        $bonCommande = (new BonCommandeService)->paginate()->first();

        $this->assertTrue($bonCommande->relationLoaded('quotation'));
        $this->assertSame($quotation->id, $bonCommande->quotation->id);
    }
}
