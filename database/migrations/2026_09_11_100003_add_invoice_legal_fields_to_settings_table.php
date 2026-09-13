<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legally-required invoice configuration for Tunisian factures:
     *
     * - fiscal_stamp_amount: the timbre fiscal (droit de timbre) applied per
     *   invoice, stored in dinars with the dinar's three-decimal precision.
     *   Null / 0 means no stamp is shown.
     * - invoice_legal_mention: free-text legal footer printed on every invoice
     *   (e.g. RC / registre de commerce, régime fiscal, penalty-for-late-payment
     *   wording).
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('fiscal_stamp_amount', 10, 3)->nullable()->after('company_tax_id');
            $table->text('invoice_legal_mention')->nullable()->after('fiscal_stamp_amount');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['fiscal_stamp_amount', 'invoice_legal_mention']);
        });
    }
};
