<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a Bon de Sortie back to the Commande it was generated from.
     *
     * A confirmed Commande can be handed out on a consignment Bon de Sortie
     * (dépôt-vente): the goods leave inventory now and the sold portion is
     * invoiced later, when the matching Bon d'Entrée regularises the exit and
     * the warehouse stock is updated. Keeping the commande_id lets the order
     * mention which exit will (eventually) sell it.
     */
    public function up(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            $table->unsignedBigInteger('commande_id')->nullable()->after('customer_id');

            $table->foreign('commande_id')->references('id')->on('commandes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            $table->dropForeign(['commande_id']);
            $table->dropColumn('commande_id');
        });
    }
};
