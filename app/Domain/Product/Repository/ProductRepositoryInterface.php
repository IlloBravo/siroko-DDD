<?php

namespace App\Domain\Product\Repository;

use App\Domain\Product\Product;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;
}