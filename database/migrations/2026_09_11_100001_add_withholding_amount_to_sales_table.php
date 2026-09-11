<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Total withholding (retenue à la source) deducted from a sale's TTC.
     * Stored in millimes (× 1000) like the per-line snapshots. Existing sales
     * default to 0 and keep behaving as if no RAS applied, so the net payable
     * equals the TTC.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('withholding_amount')->default(0)->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('withholding_amount');
        });
    }
};
