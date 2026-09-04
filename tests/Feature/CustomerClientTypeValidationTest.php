<?php

namespace Tests\Feature;

use App\Livewire\Customers\CustomerForm;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomerClientTypeValidationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsCustomerManager(): User
    {
        $permission = Permission::findOrCreate('create_customers', 'web');
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $this->actingAs($user);

        return $user;
    }

    public function test_tax_identification_number_is_required_for_a_legal_entity(): void
    {
        $this->actingAsCustomerManager();

        Livewire::test(CustomerForm::class)
            ->set('customer_name', 'Acme LLC')
            ->set('client_type', Customer::TYPE_LEGAL_ENTITY)
            ->set('customer_phone', '+21612345678')
            ->set('customer_email', 'contact@acme.test')
            ->set('tax_identification_number', '')
            ->set('city', 'Tunis')
            ->set('country', 'Tunisia')
            ->set('address', '1 Market Street')
            ->call('save')
            ->assertHasErrors(['tax_identification_number' => 'required']);
    }

    public function test_legal_entity_saves_when_tax_identification_number_is_present(): void
    {
        $this->actingAsCustomerManager();

        Livewire::test(CustomerForm::class)
            ->set('customer_name', 'Acme LLC')
            ->set('client_type', Customer::TYPE_LEGAL_ENTITY)
            ->set('customer_phone', '+21612345678')
            ->set('customer_email', 'contact@acme.test')
            ->set('tax_identification_number', '1234567A/M/000')
            ->set('city', 'Tunis')
            ->set('country', 'Tunisia')
            ->set('address', '1 Market Street')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'customer_name' => 'Acme LLC',
            'client_type' => Customer::TYPE_LEGAL_ENTITY,
            'tax_identification_number' => '1234567A/M/000',
        ]);
    }

    public function test_physical_person_saves_without_a_tax_identification_number(): void
    {
        $this->actingAsCustomerManager();

        Livewire::test(CustomerForm::class)
            ->set('customer_name', 'Jane Doe')
            ->set('client_type', Customer::TYPE_PHYSICAL_PERSON)
            ->set('customer_phone', '+21612345678')
            ->set('customer_email', 'jane@example.test')
            ->set('tax_identification_number', '')
            ->set('city', 'Sfax')
            ->set('country', 'Tunisia')
            ->set('address', '2 High Street')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'customer_name' => 'Jane Doe',
            'client_type' => Customer::TYPE_PHYSICAL_PERSON,
        ]);
    }
}
