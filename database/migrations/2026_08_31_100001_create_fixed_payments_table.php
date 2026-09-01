<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fixed payments are the recurring monthly charges (rent, utilities,
     * subscriptions, loans) attached to a given month's budget. Each one may
     * carry a stored invoice/receipt file retrievable from the archive.
     */
    public function up(): void
    {
        Schema::create('fixed_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_budget_id');
            $table->string('label');
            $table->string('category')->nullable();          // rent, utilities, subscription, loan, other
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('invoice_path')->nullable();       // stored receipt/invoice file
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('monthly_budget_id')->references('id')->on('monthly_budgets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_payments');
    }
};
