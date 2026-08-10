<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A "Bon de Sortie" (BS) records material that physically leaves the stock
     * for a loan, a job site, sub-contracting or a transfer. Each row keeps the
     * quantity that went out per product; the return quantity is filled in later
     * when the matching "Bon d'Entrée" closes the loop.
     */
    public function up(): void
    {
        Schema::create('stock_exits', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();       // BS-YYYY-00000
            $table->date('date');
            $table->string('reason')->nullable();         // Prêt / Chantier / Sous-traitance / Transfert
            $table->string('destination')->nullable();
            $table->string('responsible')->nullable();
            $table->string('status')->default('in_transit')->index(); // in_transit, closed
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('stock_exit_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_exit_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');                  // quantity taken out
            $table->integer('returned_quantity')->default(0);
            $table->integer('lost_quantity')->default(0); // consumed / lost / damaged
            $table->timestamps();

            $table->foreign('stock_exit_id')->references('id')->on('stock_exits')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_exit_details');
        Schema::dropIfExists('stock_exits');
    }
};
