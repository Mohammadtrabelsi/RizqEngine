<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legal, uninterrupted numbering counters for fiscal documents.
     *
     * Tunisian law requires sales invoices (factures) to carry a continuous,
     * non-reusable sequence. Deriving the number from the row id (max(id)+1)
     * both races under concurrency and reuses numbers after a deletion, which
     * is not compliant. Each row here is a monotonic counter that only ever
     * increases, allocated atomically under a row lock by
     * {@see \App\Services\DocumentNumberService}.
     */
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('prefix');
            $table->unsignedBigInteger('next_number')->default(1);
            $table->timestamps();
        });

        // Seed the sales counter above any invoice that already exists so the
        // switch away from max(id)+1 never re-issues an historical number.
        $nextSaleNumber = (int) (DB::table('sales')->max('id') ?? 0) + 1;

        DB::table('document_sequences')->insert([
            'key' => 'sale',
            'prefix' => 'SL',
            'next_number' => $nextSaleNumber,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
