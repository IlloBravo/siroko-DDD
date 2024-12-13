<?php

namespace App\Domain\Product\Repository;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    /**
     * @throws ProductNotFoundException
     */
    public function findByIdOrFail(UuidVO $id): Product;
    public function findAll(): Collection;
    public function save(Product $product): void;
    public function increaseStock(UuidVO $productId, int $quantity): void;
    public function decreaseStock(UuidVO $productId, int $quantity): void;
}
