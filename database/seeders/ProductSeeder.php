<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Gafas de Sol Deportivas',
                'price' => 59.99,
                'quantity' => 10,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Chaqueta Deportiva',
                'price' => 129.99,
                'quantity' => 5,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Zapatillas de Running',
                'price' => 89.99,
                'quantity' => 15,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Camiseta Técnica',
                'price' => 29.99,
                'quantity' => 20,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Pantalones Cortos Deportivos',
                'price' => 39.99,
                'quantity' => 12,
            ],
        ];

        DB::table('products')->insert($products);
    }
}
