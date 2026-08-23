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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('komoditas_id')->constrained('komoditas')->onDelete('cascade');
            $table->foreignId('satuan_id')->nullable()->constrained('satuans', 'satuan_id')->onDelete('set null');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('harga_unit', 15, 2)->nullable();
            $table->decimal('harga_supplier', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
