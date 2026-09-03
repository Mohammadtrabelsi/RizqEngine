<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->boolean('is_active')->default(true)->index();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['warehouse_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
