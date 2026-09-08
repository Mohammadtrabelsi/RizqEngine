<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Line items of a Bon de Livraison. Identical shape to commande_details.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bon_livraison_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bon_livraison_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name');
            $table->string('product_code');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('unit_price');
            $table->integer('sub_total');
            $table->integer('product_discount_amount');
            $table->string('product_discount_type')->default('fixed');
            $table->integer('product_tax_amount');
            $table->timestamps();

            $table->foreign('bon_livraison_id')->references('id')
                ->on('bon_livraisons')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')
                ->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bon_livraison_details');
    }
};
