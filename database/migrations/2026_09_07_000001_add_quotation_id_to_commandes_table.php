<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a direct Devis → Commande link so a Commande can be produced straight
 * from a Quotation (the shorter Devis → Commande → Bon de livraison → Facture
 * path), in addition to the existing Devis → Bon de Commande → Commande path.
 *
 * Unique => a Devis can be turned into at most one Commande directly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->unsignedBigInteger('quotation_id')->nullable()->unique()->after('bon_commande_id');
            $table->foreign('quotation_id')->references('id')->on('quotations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropUnique(['quotation_id']);
            $table->dropColumn('quotation_id');
        });
    }
};
