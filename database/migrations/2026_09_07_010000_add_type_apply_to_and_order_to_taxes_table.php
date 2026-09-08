<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            // 'percentage' taxes store a % in `rate`; 'fixed' taxes store a flat amount in `rate`.
            $table->string('type')->default('percentage')->after('name');
            // Where this tax can be selected: on a product, or on a purchase order/command.
            $table->string('apply_to')->default('order')->after('rate');
            // Position taxes are applied/listed in when several are combined.
            $table->integer('order')->default(0)->after('apply_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            $table->dropColumn(['type', 'apply_to', 'order']);
        });
    }
};
