<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partial shipping: trace each delivered line back to the ordered Commande line
 * it fulfils, so delivered-vs-remaining quantities can be computed per line
 * across multiple Bons de Livraison.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bon_livraison_details', function (Blueprint $table) {
            $table->unsignedBigInteger('commande_detail_id')->nullable()->after('bon_livraison_id');

            $table->foreign('commande_detail_id')->references('id')
                ->on('commande_details')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bon_livraison_details', function (Blueprint $table) {
            $table->dropForeign(['commande_detail_id']);
            $table->dropColumn('commande_detail_id');
        });
    }
};
