<?php

namespace App\Domain\Product;

final readonly class Product
{
    public function __construct(
        public string $id,
        public string $name,
        public float  $price,
        public int    $quantity
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['price'],
            $data['quantity']
        );
    }
}
