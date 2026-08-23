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
        // Migrate existing data from orders to order_items
        $orders = \Illuminate\Support\Facades\DB::table('orders')
            ->whereNotNull('komoditas_id')
            ->get();

        foreach ($orders as $order) {
            \Illuminate\Support\Facades\DB::table('order_items')->insert([
                'order_id' => $order->id,
                'komoditas_id' => $order->komoditas_id,
                'satuan_id' => $order->satuan_id,
                'jumlah' => $order->jumlah,
                'harga_unit' => $order->harga_unit,
                'harga_supplier' => $order->harga_supplier,
                'keterangan' => null,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['komoditas_id', 'satuan_id', 'jumlah', 'harga_unit', 'harga_supplier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('komoditas_id')->nullable()->constrained('komoditas')->onDelete('cascade');
            $table->foreignId('satuan_id')->nullable()->constrained('satuans', 'satuan_id')->onDelete('set null');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('harga_unit', 15, 2)->nullable();
            $table->decimal('harga_supplier', 15, 2)->nullable();
        });
    }
};
