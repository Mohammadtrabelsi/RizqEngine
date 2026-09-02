<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permissions for the Chauffeurs (drivers) and Véhicules (vehicles)
     * management modules, plus the ability to assign them on a Bon de Sortie.
     *
     * @var string[]
     */
    protected array $permissions = [
        'access_drivers',
        'create_drivers',
        'edit_drivers',
        'delete_drivers',
        'access_vehicles',
        'create_vehicles',
        'edit_vehicles',
        'delete_vehicles',
    ];

    /**
     * Roles that should be able to reach the new modules. Mirrors the
     * Sortie-Retour grants: the privileged business roles plus Admin.
     *
     * @var string[]
     */
    protected array $roles = [
        'Owner',
        'Manager',
        'Admin',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach ($this->roles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->givePermissionTo($this->permissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', $this->permissions)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
