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
                'stock' => 10,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Chaqueta Deportiva',
                'price' => 129.99,
                'stock' => 5,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Zapatillas de Running',
                'price' => 89.99,
                'stock' => 15,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Camiseta Técnica',
                'price' => 29.99,
                'stock' => 20,
            ],
            [
                'id' => UuidVO::generate(),
                'name' => 'Pantalones Cortos Deportivos',
                'price' => 39.99,
                'stock' => 12,
            ],
        ];

        DB::table('products')->insert($products);

        return $products;
    }
}
