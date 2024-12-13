<?php

namespace App\Domain\Cart;

use App\Domain\Shared\ValueObjects\UuidVO;

final class CartItem
{
    public function __construct(
        public UuidVO $id,
        public UuidVO $cartId,
        public UuidVO $productId,
        public int $quantity
    ) {}

    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            UuidVO::fromString($data->cart->id),
            UuidVO::fromString($data->product->id),
            $data->quantity
        );
    }
}