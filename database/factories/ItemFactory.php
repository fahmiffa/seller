<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipe = $this->faker->randomElement(['barang', 'jasa']);
        $harga_beli = $tipe === 'barang' ? $this->faker->numberBetween(1000, 50000) : null;
        $harga_jual = $tipe === 'barang' ? $harga_beli + $this->faker->numberBetween(1000, 20000) : $this->faker->numberBetween(5000, 100000);

        return [
            'satuan_id' => \App\Models\Satuan::inRandomOrder()->first()->satuan_id,
            'supplier_id' => null,
            'nama_item' => $this->faker->words(3, true),
            'tipe_item' => $tipe,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'stok' => $tipe === 'barang' ? $this->faker->numberBetween(0, 100) : null,
            'image' => null,
        ];
    }
}
