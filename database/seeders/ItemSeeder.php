<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure at least 5 categories
        if (\App\Models\Kategori::count() < 5) {
            \App\Models\Kategori::factory()->count(5)->create();
        }

        // Ensure at least 5 units
        if (\App\Models\Satuan::count() < 5) {
            \App\Models\Satuan::factory()->count(5)->create();
        }

        // Create 100 items
        \App\Models\Item::factory()->count(100)->create();
    }
}
