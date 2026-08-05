<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * An immutable ledger that records every change to a product's on-hand
     * quantity. Rows are append-only: each entry keeps the quantity before
     * and after the change so the full stock history can be reconstructed.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->index();            // in, out, opening, adjustment
            $table->integer('quantity');                 // magnitude of the change (always positive)
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('reference_type')->nullable(); // Sale, Purchase, Adjustment, Manual, ...
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
