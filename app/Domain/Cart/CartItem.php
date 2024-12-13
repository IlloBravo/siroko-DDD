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
        $product = Product::fromDatabase($data->product);
        $cart = Cart::fromDatabase($data->cart);

        return new self(
            UuidVO::fromString($data->id),
            $cart->id,
            $product->id,
            $data->quantity
        );
    }

    public function product(): Product
    {
        return resolve(ProductRepositoryInterface::class)->findByIdOrFail($this->productId);
    }

    public function cart(): Cart
    {
        return resolve(CartRepositoryInterface::class)->findByIdOrFail($this->cartId);
    }
}