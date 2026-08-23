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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('jumlah');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('satuan_id')->references('satuan_id')->on('satuans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['user_id', 'satuan_id']);
        });
    }
};
