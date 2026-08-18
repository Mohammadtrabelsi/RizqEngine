<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Commande — the confirmed customer order produced from a Bon de Commande.
 * Keeps the same monetary structure so it can, in turn, be invoiced (Facture).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference')->nullable();
            // Traceability: the originating Bon de Commande. Unique => a BC can
            // be converted to at most one Commande.
            $table->unsignedBigInteger('bon_commande_id')->nullable()->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->integer('tax_percentage')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('discount_percentage')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('shipping_amount')->default(0);
            $table->integer('total_amount');
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('bon_commande_id')->references('id')->on('bon_commandes')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
