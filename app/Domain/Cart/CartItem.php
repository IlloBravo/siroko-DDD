<?php

namespace App\Domain\Cart;

use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;

final class CartItem
{
    public function __construct(
        public UuidVO $id,
        public Cart $cart,
        public Product $product,
        public int $quantity
    ) {}

    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            Cart::fromDatabase((object) $data->cart),
            Product::fromDatabase((object) $data->product),
            $data->quantity
        );
    }

    public function setQuantity(int $newQuantity): void
    {
        if ($newQuantity < 1) {
            throw new \DomainException('La cantidad debe ser mayor o igual a 1.');
        }

        $this->quantity = $newQuantity;
    }

    public function incrementQuantity(int $quantity): void
    {
        $this->setQuantity($this->quantity + $quantity);
    }
}