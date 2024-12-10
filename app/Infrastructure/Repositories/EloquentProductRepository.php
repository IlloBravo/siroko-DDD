<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findByIdOrFail(string $id): ?Product
    {
        $uuid = UuidVO::fromString($id);

        $productData = DB::table('products')->where('id', (string) $uuid)->first();

        if (!$productData) {
            throw new ProductNotFoundException($id);
        }

        return Product::create((array) $productData);
    }
}
