<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fiscal context of a supplier used to decide whether, and how, a
     * withholding tax applies. Suppliers already carry a
     * tax_identification_number (matricule fiscal); these add the withholding
     * status and an optional fiscal category so rules can be extended later.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Whether the supplier is subject to withholding at all.
            $table->boolean('subject_to_withholding')->default(true)->after('tax_identification_number');
            // Free-form fiscal profile hooks for future Tunisian rules
            // (personne physique/morale, régime, activité, catégorie…).
            $table->string('legal_form')->nullable()->after('subject_to_withholding');
            $table->string('fiscal_category')->nullable()->after('legal_form');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['subject_to_withholding', 'legal_form', 'fiscal_category']);
        });
    }
};
