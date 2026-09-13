<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Partial shipping: persist an explicit shipping status on the Commande so the
 * order reflects its delivery progress — not_shipped, partially_shipped, or
 * shipped (reached only once every ordered quantity has been delivered).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('shipping_status')->default('not_shipped')->after('status');
        });

        // Backfill: before partial shipping a Commande produced at most one,
        // full delivery note — so any existing delivery note means fully shipped.
        DB::table('commandes')
            ->whereIn('id', DB::table('bon_livraisons')->select('commande_id')->whereNotNull('commande_id'))
            ->update(['shipping_status' => 'shipped']);
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn('shipping_status');
        });
    }
};
