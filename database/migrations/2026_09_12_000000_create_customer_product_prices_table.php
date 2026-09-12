<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-customer negotiated sale prices ("liste de prix par client").
     *
     * When a customer has a row here for a given product, that price
     * overrides the product's default {@see products.product_price} on sale
     * documents. The price is stored as an integer in the same unit as
     * product_price so the two are directly interchangeable.
     */
    public function up(): void
    {
        Schema::create('customer_product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('price')->default(0);
            $table->timestamps();

            // A customer holds at most one negotiated price per product.
            $table->unique(['customer_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_product_prices');
    }
};
