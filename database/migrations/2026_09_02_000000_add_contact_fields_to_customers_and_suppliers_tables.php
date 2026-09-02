<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('customer_phone');
            $table->string('responsible_person')->nullable()->after('whatsapp_number');
            $table->string('iban')->nullable()->after('tax_identification_number');
            $table->text('note')->nullable()->after('address');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('supplier_phone');
            $table->string('responsible_person')->nullable()->after('whatsapp_number');
            $table->string('iban')->nullable()->after('tax_identification_number');
            $table->text('note')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'responsible_person', 'iban', 'note']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'responsible_person', 'iban', 'note']);
        });
    }
};
