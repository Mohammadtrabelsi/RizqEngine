<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partial shipping: a Commande may now be delivered across several Bons de
 * Livraison, each shipping only a subset of the ordered quantities. Drop the
 * unique constraint on commande_id (which previously capped a Commande at one
 * delivery note) and replace it with a plain index for lookups.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bon_livraisons', function (Blueprint $table) {
            $table->dropUnique('bon_livraisons_commande_id_unique');
            $table->index('commande_id');
        });
    }

    public function down(): void
    {
        Schema::table('bon_livraisons', function (Blueprint $table) {
            $table->dropIndex(['commande_id']);
            $table->unique('commande_id');
        });
    }
};
