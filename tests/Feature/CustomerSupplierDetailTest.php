<?php

namespace Tests\Feature;

use App\Livewire\Customers\CustomerShow;
use App\Livewire\Suppliers\SupplierShow;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomerSupplierDetailTest extends TestCase
{
    use RefreshDatabase;

    private function actingWith(string ...$permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        $this->actingAs($user);

        return $user;
    }

    public function test_customer_detail_lists_orders_payments_and_credit(): void
    {
        $this->actingWith('show_customers');

        $customer = Customer::factory()->create([
            'credit_limit' => 500_00,
            'current_balance' => 60_00,
        ]);

        $sale = Sale::create([
            'date' => now()->toDateString(),
            'reference' => 'SL-TEST-1',
            'customer_id' => $customer->id,
            'customer_name' => $customer->customer_name,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 40_00,
            'total_amount' => 100_00,
            'due_amount' => 60_00,
            'status' => 'Completed',
            'payment_status' => 'Partial',
            'payment_method' => 'Cash',
            'tax_amount' => 0,
            'discount_amount' => 0,
        ]);

        SalePayment::create([
            'date' => now()->toDateString(),
            'reference' => 'PAY-TEST-1',
            'amount' => 40,
            'sale_id' => $sale->id,
            'payment_method' => 'Cash',
        ]);

        Livewire::test(CustomerShow::class, ['customer' => $customer])
            ->assertOk()
            ->assertSee('SL-TEST-1')
            ->assertSee('PAY-TEST-1')
            ->assertSee(__('customer.credit_details'))
            ->assertSee(__('customer.allowed'));
    }

    public function test_supplier_detail_lists_orders_payments_and_payable(): void
    {
        $this->actingWith('show_suppliers');

        $supplier = Supplier::factory()->create();

        $purchase = Purchase::create([
            'date' => now()->toDateString(),
            'reference' => 'PR-TEST-1',
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->supplier_name,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'paid_amount' => 30_00,
            'total_amount' => 90_00,
            'due_amount' => 60_00,
            'status' => 'Completed',
            'payment_status' => 'Partial',
            'payment_method' => 'Cash',
            'tax_amount' => 0,
            'discount_amount' => 0,
        ]);

        PurchasePayment::create([
            'date' => now()->toDateString(),
            'reference' => 'PPAY-TEST-1',
            'amount' => 30,
            'purchase_id' => $purchase->id,
            'payment_method' => 'Cash',
        ]);

        Livewire::test(SupplierShow::class, ['supplier' => $supplier])
            ->assertOk()
            ->assertSee('PR-TEST-1')
            ->assertSee('PPAY-TEST-1')
            ->assertSee(__('supplier.amount_owed'));
    }
}
