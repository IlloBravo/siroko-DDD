<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\App;
use Ramsey\Uuid\Uuid;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ejecutar ProductSeeder y obtener los productos
        $productSeeder = App::make(ProductSeeder::class);
        $products = $productSeeder->run();

        // Crear carritos usando productos existentes
        $productsCart1 = [
            $products[0], // Gafas de Sol Deportivas
            $products[1], // Chaqueta Deportiva
        ];

        $productsCart2 = [
            $products[2], // Zapatillas de Running
            $products[3], // Camiseta Técnica
        ];

        $carts = [
            [
                'id' => (string) Uuid::uuid4(),
                'items' => json_encode(array_map(fn($product) => [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                ], $productsCart1)),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'id' => (string) Uuid::uuid4(),
                'items' => json_encode(array_map(fn($product) => [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                ], $productsCart2)),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
        ];

        DB::table('carts')->insert($carts);
    }
}
