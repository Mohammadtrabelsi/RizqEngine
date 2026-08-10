<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permissions introduced with the Sortie-Retour (Bon de Sortie / Bon
     * d'Entrée) workflow.
     *
     * @var string[]
     */
    protected array $permissions = [
        'access_stock_exits',
        'create_stock_exits',
        'show_stock_exits',
        'delete_stock_exits',
        'create_stock_entries',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Grant existing Admin roles the full Sortie-Retour workflow.
        $admin = Role::where('name', 'Admin')->first();

        if ($admin) {
            $admin->givePermissionTo($this->permissions);
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
