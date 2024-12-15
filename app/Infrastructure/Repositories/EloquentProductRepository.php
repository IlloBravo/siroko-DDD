<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;
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

    public function findAll(): Collection
    {
        $productsData = DB::table('products')->get();

        return $productsData->map(function ($productData) {
            return Product::fromDatabase($productData);
        });
    }

    public function save(Product $product): void
    {
        DB::table('products')
            ->updateOrInsert(
                ['id' => (string) $product->id],
                [
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                ]
            );
    }
}