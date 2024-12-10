<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'id' => '4a781e83-bf4a-489c-b547-2e609b351bf7',
                'name' => 'Gafas',
                'price' => 10.0,
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'a9b2d3c4-5678-4def-9012-34c5678f9012',
                'name' => 'Chaqueta',
                'price' => 15.5,
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'd4e5f6a7-89ab-4cde-b012-45f6789c0123',
                'name' => 'Zapatillas',
                'price' => 25.0,
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('carts')->insert([
            [
                'id' => '656de8db-271f-4c02-b2f8-e77d1cd5c5f4',
                'items' => '[]',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
