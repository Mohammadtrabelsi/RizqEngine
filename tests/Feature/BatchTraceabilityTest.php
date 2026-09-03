<?php

namespace Tests\Feature;

use App\Enums\SerialStatus;
use App\Livewire\Batches\BatchForm;
use App\Livewire\SerialNumbers\SerialNumberIndex;
use App\Models\Batch;
use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BatchTraceabilityTest extends TestCase
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

    public function test_batch_form_creates_a_batch_with_expiry(): void
    {
        $user = $this->userWith(['access_batches', 'create_batches']);
        $product = Product::factory()->create();

        Livewire::actingAs($user)
            ->test(BatchForm::class)
            ->set('product_id', $product->id)
            ->set('batch_number', 'LOT-2026-01')
            ->set('quantity', 50)
            ->set('manufactured_date', now()->subMonth()->toDateString())
            ->set('expiry_date', now()->addYear()->toDateString())
            ->call('save');

        $this->assertDatabaseHas('batches', [
            'product_id' => $product->id,
            'batch_number' => 'LOT-2026-01',
            'quantity' => 50,
        ]);
    }

    public function test_batch_number_is_unique_per_product(): void
    {
        $user = $this->userWith(['access_batches', 'create_batches']);
        $product = Product::factory()->create();
        Batch::factory()->create(['product_id' => $product->id, 'batch_number' => 'DUP']);

        Livewire::actingAs($user)
            ->test(BatchForm::class)
            ->set('product_id', $product->id)
            ->set('batch_number', 'DUP')
            ->set('quantity', 1)
            ->call('save')
            ->assertHasErrors('batch_number');
    }

    public function test_expiry_scopes_split_expired_and_expiring(): void
    {
        $product = Product::factory()->create();
        Batch::factory()->expired()->create(['product_id' => $product->id]);
        Batch::factory()->expiringWithin(10)->create(['product_id' => $product->id]);
        Batch::factory()->create(['product_id' => $product->id, 'expiry_date' => now()->addYears(2)->toDateString()]);

        $this->assertSame(1, Batch::expired()->count());
        $this->assertSame(1, Batch::expiringWithin(30)->count());
    }

    public function test_is_expired_accessor(): void
    {
        $expired = Batch::factory()->expired()->create();
        $fresh = Batch::factory()->create(['expiry_date' => now()->addMonth()->toDateString()]);

        $this->assertTrue($expired->is_expired);
        $this->assertFalse($fresh->is_expired);
    }

    public function test_serial_number_can_be_registered_and_transitioned(): void
    {
        $user = $this->userWith(['access_serial_numbers']);
        $product = Product::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(SerialNumberIndex::class)
            ->set('product_id', $product->id)
            ->set('serial', 'SN-0001')
            ->call('register');

        $this->assertDatabaseHas('serial_numbers', [
            'serial' => 'SN-0001',
            'status' => SerialStatus::InStock->value,
        ]);

        $serial = SerialNumber::where('serial', 'SN-0001')->firstOrFail();

        $component->call('changeStatus', $serial->id, SerialStatus::Sold->value);

        $this->assertDatabaseHas('serial_numbers', [
            'id' => $serial->id,
            'status' => SerialStatus::Sold->value,
        ]);
    }

    public function test_duplicate_serial_is_rejected(): void
    {
        $user = $this->userWith(['access_serial_numbers']);
        $product = Product::factory()->create();
        SerialNumber::factory()->create(['serial' => 'SN-DUP']);

        Livewire::actingAs($user)
            ->test(SerialNumberIndex::class)
            ->set('product_id', $product->id)
            ->set('serial', 'SN-DUP')
            ->call('register')
            ->assertHasErrors('serial');
    }
}
