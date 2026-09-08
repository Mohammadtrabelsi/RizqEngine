<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permission for generating a consignment Bon de Sortie from a Commande.
 * Granted to the same business roles that manage the order workflow.
 */
return new class extends Migration
{
    private string $permission = 'convert_commandes_to_stock_exit';

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::findOrCreate($this->permission, 'web');

        foreach (['Admin', 'Owner', 'Manager'] as $roleName) {
            Role::where('name', $roleName)->first()?->givePermissionTo($this->permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::where('name', $this->permission)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
