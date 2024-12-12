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
        public int    $cartQuantity = 0
    ) {}

    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            $data->name,
            $data->price,
            $data->stock,
            $data->cartQuantity ?? 0
        );
    }

    public function decreaseStock(int $quantity): void
    {
        if ($this->stock < $quantity) {
            throw new InsufficientStockException((string) $this->id);
        }
        $this->cartQuantity += $quantity;
        $this->stock -= $quantity;
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock += $quantity;
    }

    public function decreaseCartQuantity(int $quantity): void
    {
        $this->cartQuantity -= $quantity;
    }

    public function increaseCartQuantity(int $quantity): void
    {
        $this->cartQuantity += $quantity;
    }
}
