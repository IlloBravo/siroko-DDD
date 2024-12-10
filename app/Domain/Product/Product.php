<?php

namespace App\Domain\Product;

use App\Domain\Shared\ValueObjects\UuidVO;

class Product
{
    public function __construct(
        public UuidVO $id,
        public string $name,
        public float $price,
        public int $quantity
    ) {}

    public static function create(array $data): self
    {
        return new self(
            UuidVO::fromString($data['id']),
            $data['name'],
            $data['price'],
            $data['quantity']
        );
    }
}
