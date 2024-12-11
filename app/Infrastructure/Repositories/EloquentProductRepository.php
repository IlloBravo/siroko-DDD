<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findByIdOrFail(UuidVO $id): Product
    {
        $productData = DB::table('products')->where('id', (string) $id)->first();

        if (!$productData) {
            throw new ProductNotFoundException((string) $id);
        }

        return Product::fromDatabase($productData);
    }

    public function updateStock(UuidVO $productId, int $quantity): void
    {
        DB::table('products')
            ->where('id', (string) $productId)
            ->decrement('quantity', $quantity);
    }

    public function increaseStock(UuidVO $productId, int $quantity): void
    {
        DB::table('products')
            ->where('id', (string) $productId)
            ->increment('quantity', $quantity);
    }

    public function findAll(): array
    {
        $productsData = DB::table('products')->get();

        return $productsData->map(function ($productData) {
            return Product::fromDatabase($productData);
        })->toArray();
    }

    public function save(Product $product): void
    {
        DB::table('products')
            ->where('id', (string) $product->id)
            ->update([
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'updated_at' => now(),
            ]);
    }
}