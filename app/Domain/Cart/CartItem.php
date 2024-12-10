<?php

namespace App\Domain\Cart;

use App\Domain\Product\Product;

final class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity
    ) {}

    public static function fromProduct(Product $product, int $quantity): self
    {
        return new self($product, $quantity);
    }

    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getTotalPrice(): float
    {
        return $this->product->price * $this->quantity;
    }
}
