<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findByIdOrFail(string $id): ?Product
    {
        $productData = DB::table('products')->where('id', $id)->first();

        if (!$productData) {
            throw new ProductNotFoundException($id);
        }

        return Product::fromArray([
            'id' => $productData->id,
            'name' => $productData->name,
            'price' => (float) $productData->price,
            'quantity' => (int) $productData->quantity
        ]);
    }

    public function save(Product $product): void
    {
        DB::table('products')->updateOrInsert(
            ['id' => (string) $product->id],
            [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
