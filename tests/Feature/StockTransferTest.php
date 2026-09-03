<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Livewire\StockTransfers\StockTransferForm;
use App\Livewire\Warehouses\WarehouseForm;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use App\Services\WarehouseStockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StockTransferTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
        }

        return $user;
    }

    public function test_transfer_moves_stock_between_warehouses(): void
    {
        $product = Product::factory()->create();
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();

        $warehouseStock = app(WarehouseStockService::class);
        $warehouseStock->increment($product, $from, 30);

        $transfer = app(StockTransferService::class)->create(
            $from,
            $to,
            [['product_id' => $product->id, 'quantity' => 20]],
        );

        $this->assertSame(10, $warehouseStock->quantityFor($product, $from));
        $this->assertSame(20, $warehouseStock->quantityFor($product, $to));
        $this->assertDatabaseHas('stock_transfers', ['id' => $transfer->id, 'status' => 'completed']);
        $this->assertDatabaseHas('stock_transfer_lines', [
            'stock_transfer_id' => $transfer->id,
            'product_id' => $product->id,
            'quantity' => 20,
        ]);
    }

    public function test_transfer_is_rejected_when_source_lacks_stock(): void
    {
        $product = Product::factory()->create();
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();

        app(WarehouseStockService::class)->increment($product, $from, 5);

        $this->expectException(InsufficientStockException::class);

        app(StockTransferService::class)->create(
            $from,
            $to,
            [['product_id' => $product->id, 'quantity' => 10]],
        );

        // Nothing moved.
        $this->assertDatabaseCount('stock_transfers', 0);
    }

    public function test_transfer_requires_distinct_warehouses(): void
    {
        $warehouse = Warehouse::factory()->create();

        $this->expectException(\InvalidArgumentException::class);

        app(StockTransferService::class)->create(
            $warehouse,
            $warehouse,
            [['product_id' => Product::factory()->create()->id, 'quantity' => 1]],
        );
    }

    public function test_warehouse_form_creates_a_warehouse(): void
    {
        $user = $this->userWith(['access_warehouses', 'create_warehouses']);

        Livewire::actingAs($user)
            ->test(WarehouseForm::class)
            ->set('name', 'Central Depot')
            ->set('code', 'WH-CENTRAL')
            ->set('is_default', true)
            ->call('save');

        $this->assertDatabaseHas('warehouses', [
            'name' => 'Central Depot',
            'code' => 'WH-CENTRAL',
            'is_default' => true,
        ]);
    }

    public function test_only_one_warehouse_stays_default(): void
    {
        $user = $this->userWith(['access_warehouses', 'create_warehouses']);
        $existing = Warehouse::factory()->default()->create();

        Livewire::actingAs($user)
            ->test(WarehouseForm::class)
            ->set('name', 'New Default')
            ->set('code', 'WH-NEW')
            ->set('is_default', true)
            ->call('save');

        $this->assertDatabaseHas('warehouses', ['id' => $existing->id, 'is_default' => false]);
        $this->assertSame(1, Warehouse::where('is_default', true)->count());
    }

    public function test_transfer_form_completes_a_transfer(): void
    {
        $user = $this->userWith(['access_stock_transfers', 'create_stock_transfers']);
        $product = Product::factory()->create();
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();
        app(WarehouseStockService::class)->increment($product, $from, 15);

        Livewire::actingAs($user)
            ->test(StockTransferForm::class)
            ->set('from_warehouse_id', $from->id)
            ->set('to_warehouse_id', $to->id)
            ->set('lines.0.product_id', $product->id)
            ->set('lines.0.quantity', 15)
            ->call('save');

        $this->assertSame(0, app(WarehouseStockService::class)->quantityFor($product, $from));
        $this->assertSame(15, app(WarehouseStockService::class)->quantityFor($product, $to));
    }

    public function test_transfer_form_surfaces_insufficient_stock_error(): void
    {
        $user = $this->userWith(['access_stock_transfers', 'create_stock_transfers']);
        $product = Product::factory()->create();
        $from = Warehouse::factory()->create();
        $to = Warehouse::factory()->create();
        app(WarehouseStockService::class)->increment($product, $from, 2);

        Livewire::actingAs($user)
            ->test(StockTransferForm::class)
            ->set('from_warehouse_id', $from->id)
            ->set('to_warehouse_id', $to->id)
            ->set('lines.0.product_id', $product->id)
            ->set('lines.0.quantity', 10)
            ->call('save')
            ->assertHasErrors('lines');

        $this->assertDatabaseCount('stock_transfers', 0);
    }
}
