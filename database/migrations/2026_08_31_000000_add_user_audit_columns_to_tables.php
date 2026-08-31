<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that receive `created_by` / `updated_by` audit columns.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'users',
        'categories',
        'products',
        'uploads',
        'adjustments',
        'adjusted_products',
        'expense_categories',
        'expenses',
        'customers',
        'suppliers',
        'currencies',
        'settings',
        'sales',
        'sale_details',
        'sale_payments',
        'purchases',
        'purchase_payments',
        'purchase_details',
        'sale_returns',
        'sale_return_details',
        'sale_return_payments',
        'purchase_returns',
        'purchase_return_details',
        'purchase_return_payments',
        'quotations',
        'quotation_details',
        'units',
        'stock_movements',
        'stock_exits',
        'stock_exit_details',
        'stock_entries',
        'stock_entry_details',
        'bon_commandes',
        'bon_commande_details',
        'commandes',
        'commande_details',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (! Schema::hasColumn($table, 'created_by')) {
                    $blueprint->unsignedBigInteger('created_by')->nullable()->after('id');
                    $blueprint->index('created_by');
                }

                if (! Schema::hasColumn($table, 'updated_by')) {
                    $blueprint->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                    $blueprint->index('updated_by');
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'created_by')) {
                    $blueprint->dropIndex([$table.'_created_by_index']);
                    $blueprint->dropColumn('created_by');
                }

                if (Schema::hasColumn($table, 'updated_by')) {
                    $blueprint->dropIndex([$table.'_updated_by_index']);
                    $blueprint->dropColumn('updated_by');
                }
            });
        }
    }
};
