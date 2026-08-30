<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_index_renders_without_lazy_loading_exception(): void
    {
        $permission = Permission::findOrCreate('access_user_management', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $role = Role::create(['name' => 'Manager']);
        $role->givePermissionTo($permission);

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertOk()
            ->assertSee('Manager');
    }
}
