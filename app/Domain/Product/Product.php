<?php

namespace App\Domain\Product;

use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Shared\ValueObjects\UuidVO;

class Product
{
    public function __construct(
        public UuidVO $id,
        public string $name,
        public float $price,
        public int $quantity,
        public int $cartQuantity = 0
    ) {}

    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            $data->name,
            $data->price,
            $data->quantity,
            $data->cartQuantity ?? 0
        );
    }

    public function decreaseStock(int $quantity): void
    {
        if ($this->quantity < $quantity) {
            throw new InsufficientStockException((string) $this->id);
        }

        $this->quantity -= $quantity;
    }

    public function increaseStock(int $quantity): void
    {
        $this->quantity += $quantity;
    }
}
