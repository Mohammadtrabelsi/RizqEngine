<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Sortie-Retour permissions. The original migration
     * (2026_08_10_000003_add_stock_exit_permissions) only granted these to the
     * "Admin" role, so on existing installs the Owner and Manager roles — the
     * privileged roles the seeder actually provisions — never received them and
     * the "Sortie-Retour" menu entry stayed hidden (@can('access_stock_exits')).
     *
     * This migration backfills the workflow permissions onto those business
     * roles so the menu becomes visible without a full re-seed. It mirrors the
     * grants in PermissionsTableSeeder: Owner gets everything, Manager gets the
     * full workflow too (it is not part of the Settings section).
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

    /**
     * Roles that should be able to reach the Sortie-Retour workflow.
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

        // Make sure the permissions exist (idempotent with the earlier migration).
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

        // Leave Admin untouched: the original migration owns that grant.
        foreach (['Owner', 'Manager'] as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->revokePermissionTo($this->permissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
