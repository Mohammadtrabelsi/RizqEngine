<?php

namespace Tests\Feature;

use App\Livewire\Customers\CustomerImport;
use App\Livewire\Products\ProductImport;
use App\Livewire\Suppliers\SupplierImport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['create_products', 'create_customers', 'create_suppliers'] as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }

    private function userWith(string $permission): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        return $user;
    }

    private function csv(string $contents): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('import.csv', $contents);
    }

    public function test_products_are_imported_from_a_valid_csv(): void
    {
        $this->actingAs($this->userWith('create_products'));

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $csv = "product_name,product_code,product_unit,product_quantity,product_cost,product_price,product_stock_alert,category,supplier\n"
            ."Widget,1001,pcs,5,2,4,10,{$category->id},{$supplier->id}\n";

        Livewire::test(ProductImport::class)
            ->set('file', $this->csv($csv))
            ->call('parse')
            ->assertSet('parsed', true)
            ->call('import');

        $this->assertDatabaseHas('products', [
            'product_name' => 'Widget',
            'product_code' => 1001,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_invalid_product_rows_are_reported_and_skipped(): void
    {
        $this->actingAs($this->userWith('create_products'));

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        // Row 2 is valid; row 3 references an unknown supplier and is skipped.
        $csv = "product_name,product_code,product_unit,product_quantity,product_cost,product_price,product_stock_alert,category,supplier\n"
            ."Good,2001,pcs,5,2,4,10,{$category->id},{$supplier->id}\n"
            ."Bad,2002,pcs,5,2,4,10,{$category->id},999999\n";

        $component = Livewire::test(ProductImport::class)
            ->set('file', $this->csv($csv))
            ->call('parse');

        $this->assertSame(1, $component->instance()->validCount);
        $this->assertSame(1, $component->instance()->invalidCount);

        $component->call('import');

        $this->assertDatabaseHas('products', ['product_code' => 2001]);
        $this->assertDatabaseMissing('products', ['product_code' => 2002]);
    }

    public function test_duplicate_product_code_is_rejected(): void
    {
        $this->actingAs($this->userWith('create_products'));

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        Product::factory()->create(['product_code' => 3001]);

        $csv = "product_name,product_code,product_unit,product_quantity,product_cost,product_price,product_stock_alert,category,supplier\n"
            ."Dupe,3001,pcs,5,2,4,10,{$category->id},{$supplier->id}\n";

        $component = Livewire::test(ProductImport::class)
            ->set('file', $this->csv($csv))
            ->call('parse');

        $this->assertSame(0, $component->instance()->validCount);
    }

    public function test_customers_are_imported_from_a_valid_csv(): void
    {
        $this->actingAs($this->userWith('create_customers'));

        $csv = "customer_name,customer_phone,customer_email,city,country,address\n"
            ."Acme,12345,acme@example.com,Tunis,Tunisia,1 Main St\n";

        Livewire::test(CustomerImport::class)
            ->set('file', $this->csv($csv))
            ->call('parse')
            ->call('import');

        $this->assertDatabaseHas('customers', [
            'customer_name' => 'Acme',
            'customer_email' => 'acme@example.com',
            'client_type' => Customer::TYPE_PHYSICAL_PERSON,
        ]);
    }

    public function test_legal_entity_customer_requires_tax_number(): void
    {
        $this->actingAs($this->userWith('create_customers'));

        $csv = "customer_name,client_type,customer_phone,customer_email,city,country,address,tax_identification_number\n"
            ."Legal,legal_entity,12345,legal@example.com,Tunis,Tunisia,1 Main St,\n";

        $component = Livewire::test(CustomerImport::class)
            ->set('file', $this->csv($csv))
            ->call('parse');

        $this->assertSame(0, $component->instance()->validCount);
    }

    public function test_suppliers_are_imported_from_a_valid_csv(): void
    {
        $this->actingAs($this->userWith('create_suppliers'));

        $csv = "supplier_name,supplier_phone,supplier_email,city,country,address\n"
            ."Globex,55555,globex@example.com,Sfax,Tunisia,2 Market St\n";

        Livewire::test(SupplierImport::class)
            ->set('file', $this->csv($csv))
            ->call('parse')
            ->call('import');

        $this->assertDatabaseHas('suppliers', [
            'supplier_name' => 'Globex',
            'supplier_email' => 'globex@example.com',
        ]);
    }

    public function test_import_is_forbidden_without_permission(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ProductImport::class)->assertForbidden();
    }
}
