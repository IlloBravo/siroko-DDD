<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;
use Ramsey\Uuid\Uuid;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cart = [
            'id' => (string) Uuid::uuid4(),
            'items' => collect(),
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ];

        DB::table('carts')->insert($cart);
    }
}
