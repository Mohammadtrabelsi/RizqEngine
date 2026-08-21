<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permissions introduced with the Devis → Bon de Commande → Commande → Facture
 * workflow. Granted to the same business roles that already manage Devis
 * (Admin / Owner / Manager); the Cashier role is intentionally left out.
 */
return new class extends Migration
{
    /**
     * @var string[]
     */
    protected array $permissions = [
        // Devis → Bon de Commande
        'convert_quotations',
        // Bon de Commande
        'access_bon_commandes',
        'show_bon_commandes',
        'edit_bon_commandes',
        'delete_bon_commandes',
        'confirm_bon_commandes',
        // Bon de Commande → Commande
        'convert_bon_commandes',
        // Commande
        'access_commandes',
        'show_commandes',
        'delete_commandes',
        'confirm_commandes',
        // Commande → Facture
        'convert_commandes',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (['Admin', 'Owner', 'Manager'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $role?->givePermissionTo($this->permissions);
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
