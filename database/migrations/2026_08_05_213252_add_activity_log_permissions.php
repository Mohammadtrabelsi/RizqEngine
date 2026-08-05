<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * The activity log permissions introduced with the Activitylog module.
     *
     * @var string[]
     */
    protected array $permissions = [
        'access_activity_logs',
        'delete_activity_logs',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Grant existing Admin roles read-only access to the audit trail
        // (deleting logs stays reserved for the Super Admin).
        $admin = Role::where('name', 'Admin')->first();

        if ($admin) {
            $admin->givePermissionTo('access_activity_logs');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', $this->permissions)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
