<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use Illuminate\Support\Facades\File;

class ImportItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $jsonPath = base_path('../items.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File not found at: $jsonPath");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!$data) {
            $this->command->error("Invalid JSON data.");
            return;
        }

        $userId = 1; // Default user ID
        $satuanId = 6; // Default satuan ID as requested

        // Check if user 1 exists, if not pick the first one
        $user = \App\Models\User::find($userId);
        if (!$user) {
            $user = \App\Models\User::first();
            if ($user) {
                $userId = $user->id;
                $this->command->info("User ID 1 not found. Using User ID: $userId");
            } else {
                $this->command->error("No users found in database. Please create a user first.");
                return;
            }
        }

        // Ensure satuan exists
        $satuan = \App\Models\Satuan::find($satuanId);
        if (!$satuan) {
            // Try to create it or fail? 
            // Better to assume it might not exist and warn, but user specifically asked for 6.
            // If strict, I should fail. 
            $this->command->warn("Satuan ID $satuanId not found. Creating it temporarily or ensure it exists.");
            // Auto-create for safety if missing, assigning to user
            \App\Models\Satuan::firstOrCreate(
                ['satuan_id' => $satuanId],
                ['nama_satuan' => 'Pcs', 'user_id' => $userId]
            );
        }

        $count = 0;
        foreach ($data as $item) {
            Item::create([
                'user_id' => $userId,
                'satuan_id' => $satuanId,
                'nama_item' => $item['name'],
                'tipe_item' => 'barang',
                'harga_beli' => 0, // Defaulting to 0 since not provided
                'harga_jual' => $item['price'],
                'stok' => 0, // Defaulting to 0
                'supplier_id' => null,
                'image' => null,
                'expired_at' => null,
            ]);
            $count++;
        }

        $this->command->info("Successfully imported $count items.");
    }
}
