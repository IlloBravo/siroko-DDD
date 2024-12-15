<?php

namespace App\Domain\Cart;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
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
            UuidVO::fromString($data->cart_id),
            UuidVO::fromString($data->product_id),
            $data->quantity
        );
    }

    public function product(): Product
    {
        return app(ProductRepositoryInterface::class)->findByIdOrFail($this->productId);
    }

    public function cart(): Cart
    {
        return app(CartRepositoryInterface::class)->findByIdOrFail($this->cartId);
    }
}