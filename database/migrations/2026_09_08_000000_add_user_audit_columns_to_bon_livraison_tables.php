<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The bon_livraisons / bon_livraison_details tables were created (2026_09_07)
 * after the sweeping audit-column migration (2026_08_31) had already run, so
 * they never received the `created_by` / `updated_by` columns their models
 * expect via the TracksUserActions trait. This backfills them for databases
 * that were migrated before the create-migrations were amended.
 *
 * @var array<int, string>
 */
return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = [
        'bon_livraisons',
        'bon_livraison_details',
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
