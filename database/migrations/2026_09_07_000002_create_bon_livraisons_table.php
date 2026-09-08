<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bon de Livraison (BL) — the delivery note produced from a Commande. It records
 * the physical hand-over of the ordered goods and can, in turn, optionally be
 * invoiced (Facture). It keeps the same monetary structure as the Commande so an
 * optional Facture generated from it carries the exact same amounts and taxes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bon_livraisons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->date('date');
            $table->string('reference')->nullable();
            // Traceability: the originating Commande. Unique => a Commande yields
            // at most one Bon de Livraison.
            $table->unsignedBigInteger('commande_id')->nullable()->unique();
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

            $table->foreign('commande_id')->references('id')->on('commandes')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_livraisons');
    }
};
