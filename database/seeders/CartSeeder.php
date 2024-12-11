<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DateTime;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productsCart1 = [
            Product::create([
                'id' => (string) Str::uuid(),
                'name' => 'Gafas de Sol',
                'price' => 49.99,
                'quantity' => 2,
            ]),
            Product::create([
                'id' => (string) Str::uuid(),
                'name' => 'Chaqueta de Cuero',
                'price' => 129.99,
                'quantity' => 1,
            ]),
        ];

        $productsCart2 = [
            Product::create([
                'id' => (string) Str::uuid(),
                'name' => 'Zapatillas Deportivas',
                'price' => 89.99,
                'quantity' => 1,
            ]),
            Product::create([
                'id' => (string) Str::uuid(),
                'name' => 'Camiseta Básica',
                'price' => 19.99,
                'quantity' => 3,
            ]),
        ];

        $carts = [
            [
                'id' => (string) Str::uuid(),
                'items' => json_encode(array_map(fn($product) => [
                    'id' => (string) $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                ], $productsCart1)),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'id' => (string) Str::uuid(),
                'items' => json_encode(array_map(fn($product) => [
                    'id' => (string) $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                ], $productsCart2)),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
        ];

        DB::table('carts')->insert($carts);
    }
}
