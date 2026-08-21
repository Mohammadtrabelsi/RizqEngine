<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bon de Commande (BC) — a customer purchase order produced by transforming an
 * accepted Devis (Quotation). Mirrors the quotations table so the whole
 * Devis pricing/tax/discount/total structure is preserved 1:1.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bon_commandes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference')->nullable();
            // Traceability: the originating Devis. Unique => a Devis can be
            // converted to at most one Bon de Commande (atomic guard).
            $table->unsignedBigInteger('quotation_id')->nullable()->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->integer('tax_percentage')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('discount_percentage')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('shipping_amount')->default(0);
            $table->integer('total_amount');
            $table->string('status')->default('draft');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('quotation_id')->references('id')->on('quotations')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_commandes');
    }
};
