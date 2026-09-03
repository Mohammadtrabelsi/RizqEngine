<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Credit-sale tracking for customers. Amounts are stored in cents to match
     * the existing sales money columns (paid_amount, due_amount, ...).
     *
     *  - credit_limit:    maximum outstanding balance allowed on credit
     *                     (0 = no credit permitted).
     *  - current_balance: amount currently owed by the customer.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_limit')->default(0)->after('note');
            $table->unsignedBigInteger('current_balance')->default(0)->after('credit_limit');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_limit', 'current_balance']);
        });
    }
};
