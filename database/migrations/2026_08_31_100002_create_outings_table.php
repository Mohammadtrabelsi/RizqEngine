<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An outing ("bon de sortie") is a discrete event with a date, a
     * location/purpose and a list of participants. Its cost is split across
     * fixed itemized categories; a voucher file is generated and stored on save.
     */
    public function up(): void
    {
        Schema::create('outings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();            // BS-YYYY-00000
            $table->date('date');
            $table->string('location')->nullable();
            $table->string('purpose')->nullable();
            $table->json('participants')->nullable();
            $table->decimal('food', 15, 2)->default(0);
            $table->decimal('gas', 15, 2)->default(0);
            $table->decimal('water', 15, 2)->default(0);
            $table->decimal('transport', 15, 2)->default(0);
            $table->decimal('misc', 15, 2)->default(0);
            $table->string('voucher_path')->nullable();       // generated voucher/receipt file
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outings');
    }
};
