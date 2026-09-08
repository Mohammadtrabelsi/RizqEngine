<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The company's matricule fiscal, required on a withholding-tax
     * certificate (certificat de retenue à la source) as the identifier of the
     * party performing the withholding.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('company_tax_id')->nullable()->after('company_address');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('company_tax_id');
        });
    }
};
