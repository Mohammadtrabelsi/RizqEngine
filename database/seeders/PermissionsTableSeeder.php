<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // User Mangement
            'edit_own_profile',
            'access_user_management',
            // Dashboard
            'show_total_stats',
            'show_month_overview',
            'show_weekly_sales_purchases',
            'show_monthly_cashflow',
            'show_notifications',
            // Products
            'access_products',
            'create_products',
            'show_products',
            'edit_products',
            'delete_products',
            // Product Categories
            'access_product_categories',
            // Barcode Printing
            'print_barcodes',
            // Adjustments
            'access_adjustments',
            'create_adjustments',
            'show_adjustments',
            'edit_adjustments',
            'delete_adjustments',
            // Stock Exits / Entries (Sortie-Retour)
            'access_stock_exits',
            'create_stock_exits',
            'show_stock_exits',
            'delete_stock_exits',
            'create_stock_entries',
            // Drivers (Chauffeurs)
            'access_drivers',
            'create_drivers',
            'edit_drivers',
            'delete_drivers',
            // Vehicles (Véhicules)
            'access_vehicles',
            'create_vehicles',
            'edit_vehicles',
            'delete_vehicles',
            // Quotaions
            'access_quotations',
            'create_quotations',
            'show_quotations',
            'edit_quotations',
            'delete_quotations',
            // Create Sale From Quotation
            'create_quotation_sales',
            // Send Quotation On Email
            'send_quotation_mails',
            // Devis → Bon de Commande → Commande → Facture workflow
            'convert_quotations',
            'access_bon_commandes',
            'show_bon_commandes',
            'edit_bon_commandes',
            'delete_bon_commandes',
            'confirm_bon_commandes',
            'convert_bon_commandes',
            'access_commandes',
            'show_commandes',
            'delete_commandes',
            'confirm_commandes',
            'convert_commandes',
            // Expenses
            'access_expenses',
            'create_expenses',
            'edit_expenses',
            'delete_expenses',
            // Expense Categories
            'access_expense_categories',
            // Customers
            'access_customers',
            'create_customers',
            'show_customers',
            'edit_customers',
            'delete_customers',
            // Suppliers
            'access_suppliers',
            'create_suppliers',
            'show_suppliers',
            'edit_suppliers',
            'delete_suppliers',
            // Sales
            'access_sales',
            'create_sales',
            'show_sales',
            'edit_sales',
            'delete_sales',
            // POS Sale
            'create_pos_sales',
            // Sale Payments
            'access_sale_payments',
            // Sale Returns
            'access_sale_returns',
            'create_sale_returns',
            'show_sale_returns',
            'edit_sale_returns',
            'delete_sale_returns',
            // Sale Return Payments
            'access_sale_return_payments',
            // Purchases
            'access_purchases',
            'create_purchases',
            'show_purchases',
            'edit_purchases',
            'delete_purchases',
            // Purchase Payments
            'access_purchase_payments',
            // Purchase Returns
            'access_purchase_returns',
            'create_purchase_returns',
            'show_purchase_returns',
            'edit_purchase_returns',
            'delete_purchase_returns',
            // Purchase Return Payments
            'access_purchase_return_payments',
            // Reports
            'access_reports',
            // Currencies
            'access_currencies',
            'create_currencies',
            'edit_currencies',
            'delete_currencies',
            // Settings
            'access_settings',
            // Units
            'access_units',
            // Activity Logs
            'access_activity_logs',
            'delete_activity_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role = Role::firstOrCreate([
            'name' => 'Admin',
        ]);

        $role->givePermissionTo($permissions);
        $role->revokePermissionTo('access_user_management');
        // Only the Super Admin may purge the audit trail.
        $role->revokePermissionTo('delete_activity_logs');

        $this->seedBusinessRoles($permissions);
    }

    /**
     * Seed the three business roles shown in the RizqEngine design system.
     * These gate the sidebar via the same @can permission checks the app
     * already uses — a user's role determines exactly which nav sections
     * (and screens) they can reach.
     *
     *   Owner   — full access to everything.
     *   Manager — day-to-day oversight; everything except the Settings
     *             section (system settings, currencies, units) and audit purge.
     *   Cashier — the till: dashboard, product lookup, sales, POS and returns.
     */
    protected function seedBusinessRoles(array $permissions): void
    {
        // Settings section — the only thing a Manager may not touch.
        $settingsPermissions = [
            'access_settings',
            'access_currencies', 'create_currencies', 'edit_currencies', 'delete_currencies',
            'access_units',
        ];

        // Owner: everything.
        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $owner->syncPermissions($permissions);

        // Manager: everything except the Settings section and audit purge.
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->syncPermissions(array_values(array_diff(
            $permissions,
            array_merge($settingsPermissions, ['delete_activity_logs'])
        )));

        // Cashier: sales floor only.
        $cashier = Role::firstOrCreate(['name' => 'Cashier']);
        $cashier->syncPermissions([
            'edit_own_profile',
            // Dashboard
            'show_total_stats',
            'show_weekly_sales_purchases',
            'show_notifications',
            // Products & inventory (read-only lookup)
            'access_products',
            'show_products',
            'access_product_categories',
            'print_barcodes',
            // Sales & POS
            'access_sales',
            'create_sales',
            'show_sales',
            'create_pos_sales',
            'access_sale_payments',
            // Returns
            'access_sale_returns',
            'create_sale_returns',
            'show_sale_returns',
            'access_sale_return_payments',
            // Customers (needed to ring up a sale)
            'access_customers',
            'create_customers',
            'show_customers',
        ]);
    }
}
