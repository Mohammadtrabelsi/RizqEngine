<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MenuVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_parties_menu_appears_when_user_has_customer_or_supplier_access(): void
    {
        Permission::findOrCreate('access_customers', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo('access_customers');

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            // The redesign groups these links under a "Customers & Suppliers"
            // heading; the group and the Customers link must appear for a user
            // who has customer access.
            ->assertSee('Customers')
            ->assertSee('Suppliers');
    }
}
