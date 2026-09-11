<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Sale;
use App\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceSequentialNumberingTest extends TestCase
{
    use RefreshDatabase;

    private function makeSale(Customer $customer): Sale
    {
        return Sale::create([
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => 0,
            'due_amount' => 0,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'payment_method' => 'Cash',
            'tax_amount' => 0,
            'discount_amount' => 0,
        ]);
    }

    public function test_invoices_get_a_continuous_sequence(): void
    {
        $customer = Customer::factory()->create();

        $first = $this->makeSale($customer);
        $second = $this->makeSale($customer);
        $third = $this->makeSale($customer);

        $this->assertSame('SL-00001', $first->reference);
        $this->assertSame('SL-00002', $second->reference);
        $this->assertSame('SL-00003', $third->reference);
    }

    public function test_a_deleted_invoice_number_is_never_reused(): void
    {
        $customer = Customer::factory()->create();

        $first = $this->makeSale($customer);
        $second = $this->makeSale($customer);

        // Deleting the latest invoice must not free its number: the next one
        // continues the sequence rather than re-issuing SL-00002.
        $second->delete();

        $third = $this->makeSale($customer);

        $this->assertSame('SL-00003', $third->reference);
    }

    public function test_reference_is_immutable_and_not_taken_from_input(): void
    {
        $customer = Customer::factory()->create();

        // Even if a reference is supplied on creation, the counter wins.
        $sale = Sale::create([
            'reference' => 'HACK-999',
            'date' => '2026-01-01',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 0,
            'total_amount' => 0,
            'due_amount' => 0,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'payment_method' => 'Cash',
            'tax_amount' => 0,
            'discount_amount' => 0,
        ]);

        $this->assertSame('SL-00001', $sale->reference);
    }

    public function test_service_allocates_the_next_number_for_a_fresh_key(): void
    {
        $service = app(DocumentNumberService::class);

        $a = $service->next('custom-doc', 'CD');
        $b = $service->next('custom-doc', 'CD');

        $this->assertSame('CD-00001', $a);
        $this->assertSame('CD-00002', $b);
    }
}
