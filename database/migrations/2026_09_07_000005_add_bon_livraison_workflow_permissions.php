<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permissions introduced with the Devis → Commande → Bon de Livraison → Facture
 * path. Granted to the same business roles that already manage the Devis
 * workflow (Admin / Owner / Manager); the Cashier role is intentionally left out.
 */
return new class extends Migration
{
    /**
     * @var string[]
     */
    protected array $permissions = [
        // Devis → Commande (direct)
        'convert_quotations_to_commande',
        // Commande → Bon de Livraison
        'convert_commandes_to_bon_livraison',
        // Bon de Livraison
        'access_bon_livraisons',
        'show_bon_livraisons',
        'delete_bon_livraisons',
        'deliver_bon_livraisons',
        // Bon de Livraison → Facture
        'convert_bon_livraisons',
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
