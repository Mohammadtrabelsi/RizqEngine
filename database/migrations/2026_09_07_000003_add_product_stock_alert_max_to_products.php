<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional per-product high-stock (overstock) alert threshold. When set, a
 * product whose on-hand quantity exceeds this value is flagged as
 * StockStatus::HighStock instead of InStock.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('product_stock_alert_max')->nullable()->after('product_stock_alert');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_stock_alert_max');
        });
    }
};
