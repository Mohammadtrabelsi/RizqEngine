<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Default tax entry mode for each side of the ledger. Purchase-side
     * documents (achats / bons de commande) and sale-side documents (devis,
     * commandes, factures) each open with the mode configured here — either
     * "included" (Taxe incluse / TTC) or "excluded" (Hors taxes / HT) — so the
     * whole application reflects how the tenant buys and sells.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('purchase_tax_mode')->default('included')->after('company_tax_id');
            $table->string('sale_tax_mode')->default('included')->after('purchase_tax_mode');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['purchase_tax_mode', 'sale_tax_mode']);
        });
    }
};
