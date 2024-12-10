<?php

namespace App\Domain\Product\Repository;

use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;

interface ProductRepositoryInterface
{
    /**
     * @throws ProductNotFoundException
     */
    public function findByIdOrFail(string $id): ?Product;
}