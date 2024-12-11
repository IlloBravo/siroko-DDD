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
        $productSeeder = App::make(ProductSeeder::class);
        $products = $productSeeder->run();

        // Establecer la cantidad específica para los productos en el carrito
        $productsCart1 = [
            $this->prepareProductForCart($products[0], 2), // Gafas de Sol Deportivas
            $this->prepareProductForCart($products[1], 1), // Chaqueta Deportiva
        ];

        $productsCart2 = [
            $this->prepareProductForCart($products[2], 3), // Zapatillas de Running
            $this->prepareProductForCart($products[3], 2), // Camiseta Técnica
        ];

        $carts = [
            [
                'id' => (string) Uuid::uuid4(),
                'items' => json_encode(array_map(fn($product) => [
                    'id' => (string) $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'cartQuantity' => $product['cartQuantity'],
                ], $productsCart1)),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
            [
                'id' => (string) Uuid::uuid4(),
                'items' => json_encode(array_map(fn($product) => [
                    'id' => (string) $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'cartQuantity' => $product['cartQuantity'],
                ], $productsCart2)),
                'created_at' => new DateTime(),
                'updated_at' => new DateTime(),
            ],
        ];

        DB::table('carts')->insert($carts);
    }

    private function prepareProductForCart($product, int $cartQuantity)
    {
        $product['cartQuantity'] = $cartQuantity;
        $product['quantity'] -= $cartQuantity;
        return $product;
    }
}
