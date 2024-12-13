<?php

namespace App\Domain\Product;

use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Shared\ValueObjects\UuidVO;

class Product
{
    public function __construct(
        public UuidVO $id,
        public string $name,
        public float  $price,
        public int    $stock,
    ) {}

    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            $data->name,
            $data->price,
            $data->stock,
        );
    }

    public function decreaseStock(int $quantity): void
    {
        if ($this->stock < $quantity) {
            throw new InsufficientStockException((string) $this->id);
        }
        $this->stock -= $quantity;
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock += $quantity;
    }
}
