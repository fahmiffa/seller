<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Suppress foreign key checks to delete all related data safely if needed
        // But better to do it cleanly:

        // 1. Delete details that reference items (since items reference categories)
        \DB::table('detail_transaksis')->delete();
        \DB::table('detail_pembelians')->delete();

        // 2. Delete all items (since they all rely on categories)
        \DB::table('items')->delete();

        // 3. Update items table schema
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        // 4. Drop categories table
        Schema::dropIfExists('kategoris');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, recovery is complex.
        // Usually, we'd recreate the table and column here.
    }
};
