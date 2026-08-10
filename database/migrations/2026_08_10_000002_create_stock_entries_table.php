<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A "Bon d'Entrée" (BE) closes a "Bon de Sortie". It records how much of the
     * material that went out actually came back and how much was consumed or
     * lost. A BE can never exist without referencing a validated BS.
     */
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();        // BE-YYYY-00000
            $table->unsignedBigInteger('stock_exit_id');   // mandatory link to the origin BS
            $table->date('date');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('stock_exit_id')->references('id')->on('stock_exits')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('stock_entry_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_entry_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_out');              // originally taken out (snapshot)
            $table->integer('quantity_returned');         // physically received back
            $table->integer('quantity_lost');             // consumed / lost / damaged
            $table->timestamps();

            $table->foreign('stock_entry_id')->references('id')->on('stock_entries')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_entry_details');
        Schema::dropIfExists('stock_entries');
    }
};
