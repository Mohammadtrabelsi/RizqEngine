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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('tax_identification_number')->nullable()->after('customer_phone');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('tax_identification_number')->nullable()->after('supplier_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('tax_identification_number');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('tax_identification_number');
        });
    }
};
