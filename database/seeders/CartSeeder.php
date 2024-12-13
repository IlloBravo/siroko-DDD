<?php

namespace Database\Seeders;

use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cart = [
            'id' => UuidVO::generate(),
            'items' => collect(),
        ];

        DB::table('carts')->insert($cart);
    }
}
