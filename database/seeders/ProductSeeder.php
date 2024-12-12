<?php

namespace Database\Seeders;

use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): array
    {
        $products = [
            [
                'id' => UuidVO::generate(),
                'name' => 'Gafas de Sol Deportivas',
                'price' => 59.99,
                'quantity' => 10,
                'cartQuantity' => 0,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Chaqueta Deportiva',
                'price' => 129.99,
                'quantity' => 5,
                'cartQuantity' => 0,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Zapatillas de Running',
                'price' => 89.99,
                'quantity' => 15,
                'cartQuantity' => 0,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Camiseta Técnica',
                'price' => 29.99,
                'quantity' => 20,
                'cartQuantity' => 0,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Pantalones Cortos Deportivos',
                'price' => 39.99,
                'quantity' => 12,
                'cartQuantity' => 0,
            ],
        ];

        DB::table('products')->insert($products);

        return $products;
    }
}
