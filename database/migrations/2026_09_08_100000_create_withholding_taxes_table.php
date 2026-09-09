<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master data for withholding taxes (retenues à la source / RAS).
     * Rates are configured here by administrators and never hardcoded.
     */
    public function up(): void
    {
        Schema::create('withholding_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            // Percentage rate (0–100) applied to the calculation base.
            $table->decimal('rate', 8, 3)->default(0);
            // One of App\Enums\WithholdingCalculationBase: ht | tva | ttc.
            $table->string('calculation_base')->default('ttc');
            $table->boolean('active')->default(true);
            $table->boolean('applicable_to_purchases')->default(true);
            $table->boolean('applicable_to_sales')->default(false);
            // Optional validity window; null means no bound on that side.
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withholding_taxes');
    }
};
