<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DateTime;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cart = [
            'id' => (string) Str::uuid(),
            'items' => json_encode([]),
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ];

        DB::table('carts')->insert($cart);
    }
}
