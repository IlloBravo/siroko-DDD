<?php

namespace App\Domain\Cart;

use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;

final class CartItem
{
    public function __construct(
        public UuidVO $id,
        public Cart $cart,
        public Product $product,
        public int $quantity
    ) {}

    /**
     * @throws DateMalformedStringException
     */
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
        $this->quantity = $newQuantity;
    }
}