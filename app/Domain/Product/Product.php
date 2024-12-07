<?php

namespace App\Domain\Product;

class Product
{
    public function __construct(
        public string $id,
        public string $name,
        public float $price,
        public int $quantity
    ) {}
}
