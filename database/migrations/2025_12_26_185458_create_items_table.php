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
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('kategori_id')->constrained('kategoris', 'kategori_id');
            $table->foreignId('satuan_id')->constrained('satuans', 'satuan_id');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers', 'supplier_id');
            $table->string('nama_item');
            $table->enum('tipe_item', ['barang', 'jasa']);
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->decimal('harga_jual', 15, 2);
            $table->integer('stok')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
