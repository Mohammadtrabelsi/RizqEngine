<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a Sale (Facture) back to the Commande it was generated from.
 * Unique => a Commande can be invoiced at most once (atomic guard against
 * duplicate invoices).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('commande_id')->nullable()->unique()->after('id');
            $table->foreign('commande_id')->references('id')->on('commandes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['commande_id']);
            $table->dropUnique(['commande_id']);
            $table->dropColumn('commande_id');
        });
    }
};
