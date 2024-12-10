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
                'name' => 'Gafas',
                'price' => 59.99,
                'quantity' => 10,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Chaqueta',
                'price' => 129.99,
                'quantity' => 5,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Zapatillas',
                'price' => 89.99,
                'quantity' => 15,
            ],
        ];

        DB::table('products')->insert($products);
    }
}
