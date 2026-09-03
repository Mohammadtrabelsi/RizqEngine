<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the "Bon de Sortie Provisoire / Sortie pour dépôt" (consignment /
     * dépôt-vente) flow on top of the existing Bon de Sortie.
     *
     * A consignment exit hands goods to a reseller, a sales rep or an event.
     * The stock leaves the main inventory into a virtual "dépôt", but nothing
     * is invoiced yet. At the end of the period a Bon de Retour records the
     * unsold quantity that physically came back; the difference
     *
     *     Stock Vendu = Q_init - Q_retour
     *
     * is what was actually sold and is invoiced at that moment (régularisation).
     */
    public function up(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            // standard = loan/job-site/transfer (returns, no sale)
            // consignment = dépôt-vente (unsold returns, sold portion invoiced)
            $table->string('kind')->default('standard')->after('reference')->index();
            // The reseller / sales rep the goods are consigned to; drives the
            // invoice generated at régularisation.
            $table->unsignedBigInteger('customer_id')->nullable()->after('kind');

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });

        Schema::table('stock_exit_details', function (Blueprint $table) {
            // Quantity actually sold at régularisation (Q_init - Q_retour).
            $table->integer('sold_quantity')->default(0)->after('lost_quantity');
            // Unit price snapshot (in cents) used to bill the sold quantity.
            $table->integer('unit_price')->nullable()->after('sold_quantity');
        });

        Schema::table('stock_entries', function (Blueprint $table) {
            // The invoice generated when a consignment exit is regularised.
            $table->unsignedBigInteger('sale_id')->nullable()->after('stock_exit_id');

            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropColumn('sale_id');
        });

        Schema::table('stock_exit_details', function (Blueprint $table) {
            $table->dropColumn(['sold_quantity', 'unit_price']);
        });

        Schema::table('stock_exits', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['kind', 'customer_id']);
        });
    }
};
