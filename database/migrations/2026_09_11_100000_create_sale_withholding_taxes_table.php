<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-sale snapshot of each applied withholding tax (retenue à la source).
     * Mirrors {@see purchase_withholding_taxes}: the name / code / rate / base
     * are copied at invoice time so later edits to the master withholding tax
     * never rewrite historical documents.
     *
     * Monetary amounts are stored in millimes (× 1000) to preserve the
     * Tunisian dinar's three-decimal precision on RAS figures and certificates.
     */
    public function up(): void
    {
        Schema::create('sale_withholding_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            // Nullable so a snapshot survives deletion of the master record.
            $table->foreignId('withholding_tax_id')->nullable()
                ->constrained('withholding_taxes')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->decimal('rate', 8, 3)->default(0);
            $table->string('calculation_base');
            $table->bigInteger('taxable_amount')->default(0);
            $table->bigInteger('amount')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_withholding_taxes');
    }
};
