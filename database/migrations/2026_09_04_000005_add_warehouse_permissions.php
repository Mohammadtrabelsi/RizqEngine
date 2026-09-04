<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permissions for the multi-warehouse (dépôts) and stock-transfer modules.
     *
     * @var string[]
     */
    protected array $permissions = [
        'access_warehouses',
        'create_warehouses',
        'edit_warehouses',
        'delete_warehouses',
        'access_stock_transfers',
        'create_stock_transfers',
    ];

    /**
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
