<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Role grants for the permissions introduced with the Spatie integration.
     *
     *  - access_system_monitoring: the System status screen (health checks and
     *    backup status). Kept to Owner / Admin; Managers and Cashiers do not
     *    see infrastructure details. Super Admin passes every gate anyway.
     *  - export_products: download the product catalogue (incl. cost prices)
     *    as CSV / Excel.
     *
     * @var array<string, array<int, string>>
     */
    protected array $grants = [
        'access_system_monitoring' => ['Owner', 'Admin'],
        'export_products' => ['Owner', 'Admin', 'Manager'],
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->grants as $permission => $roles) {
            Permission::findOrCreate($permission, 'web');

            foreach ($roles as $roleName) {
                Role::where('name', $roleName)->first()?->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', array_keys($this->grants))->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
