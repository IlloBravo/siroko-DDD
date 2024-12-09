<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(string $id): ?Product
    {
        $productData = DB::table('products')->where('id', $id)->first();

        if (!$productData) {
            return null;
        }

        return new Product(
            $productData->id,
            $productData->name,
            (float) $productData->price,
            (int) $productData->quantity
        );
    }
}
