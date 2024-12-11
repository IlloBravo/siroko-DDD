<?php

namespace App\Domain\Product\Repository;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;

interface ProductRepositoryInterface
{
    /**
     * @throws ProductNotFoundException
     */
    public function findByIdOrFail(UuidVO $id): Product;
    public function findAll(): array;
    public function increaseStock(UuidVO $productId, int $quantity): void;
}