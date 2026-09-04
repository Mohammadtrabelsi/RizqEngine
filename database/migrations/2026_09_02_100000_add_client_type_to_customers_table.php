<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Classifies a customer as either a physical person or a legal entity.
     * A tax identification number (matricule fiscal) is required for legal
     * entities; the conditional requirement is enforced at validation time
     * (see App\Livewire\Customers\CustomerForm) since the column itself must
     * stay nullable to accommodate physical persons.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('client_type')->default('physical_person')->after('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('client_type');
        });
    }
};
