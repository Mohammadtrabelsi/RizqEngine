<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Product;
use App\Models\StockExit;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DriverVehicleTest extends TestCase
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

    public function test_a_driver_can_be_created_through_the_form(): void
    {
        $user = $this->userWith(['access_drivers', 'create_drivers']);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Drivers\DriverForm::class)
            ->set('name', 'Ali Ben Salah')
            ->set('phone', '20123456')
            ->set('license_number', 'TN-42')
            ->call('save');

        $this->assertDatabaseHas('drivers', [
            'name' => 'Ali Ben Salah',
            'phone' => '20123456',
            'license_number' => 'TN-42',
        ]);
    }

    public function test_a_vehicle_can_be_created_through_the_form(): void
    {
        $user = $this->userWith(['access_vehicles', 'create_vehicles']);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Vehicles\VehicleForm::class)
            ->set('registration', '123-TUN-45')
            ->set('brand', 'Renault')
            ->set('model', 'Master')
            ->call('save');

        $this->assertDatabaseHas('vehicles', [
            'registration' => '123-TUN-45',
            'brand' => 'Renault',
            'model' => 'Master',
        ]);
    }

    public function test_a_stock_exit_stores_the_selected_driver_and_vehicle(): void
    {
        $user = $this->userWith(['create_stock_exits']);
        $product = Product::factory()->create(['product_quantity' => 10]);
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->actingAs($user)->post(route('stock-exits.store'), [
            'date' => now()->format('Y-m-d'),
            'reason' => 'Chantier',
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'product_ids' => [$product->id],
            'quantities' => [3],
        ])->assertRedirect(route('stock-exits.index'));

        $this->assertDatabaseHas('stock_exits', [
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $this->assertSame(1, StockExit::count());
    }
}
