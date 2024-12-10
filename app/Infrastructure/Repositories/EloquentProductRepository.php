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
            'stock' => (int) $productData->stock
        ]);
    }

    public function save(Product $product): void
    {
        DB::table('products')->updateOrInsert(
            ['id' => (string) $product->id],
            [
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
