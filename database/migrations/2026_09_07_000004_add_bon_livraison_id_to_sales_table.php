<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a Sale (Facture) back to the Bon de Livraison it was generated from.
 * Unique => a Bon de Livraison is invoiced at most once.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('bon_livraison_id')->nullable()->unique()->after('commande_id');
            $table->foreign('bon_livraison_id')->references('id')->on('bon_livraisons')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['bon_livraison_id']);
            $table->dropUnique(['bon_livraison_id']);
            $table->dropColumn('bon_livraison_id');
        });
    }
};
