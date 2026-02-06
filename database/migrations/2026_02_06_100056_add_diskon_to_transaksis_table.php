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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('metode_pembayaran'); // Harga sebelum diskon
            $table->decimal('diskon', 15, 2)->default(0)->after('subtotal'); // Nominal diskon
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'diskon']);
        });
    }
};
