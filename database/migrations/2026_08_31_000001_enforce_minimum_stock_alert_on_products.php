<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Enforce the business rule that every product's low-stock alert threshold is
 * at least 10, so any product dropping below 10 units is flagged as low stock.
 * Backfills existing products whose threshold is currently below 10.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('product_stock_alert', '<', 10)
            ->update(['product_stock_alert' => 10]);
    }

    public function down(): void
    {
        // No-op: previous per-product thresholds are not retained.
    }
};
