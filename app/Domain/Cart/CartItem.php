<?php

namespace App\Domain\Cart;

use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Product;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
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

    public static function create(Cart $cart, Product $product, int $quantity): self
    {
        if ($quantity < 1) {
            throw new InvalidQuantityException($quantity);
        }

        if (!$product->hasSufficientStock($quantity)) {
            throw new InsufficientStockException($product->id);
        }

        return new self(UuidVO::generate(), $cart->id, $product->id, $quantity);
    }

    public function setQuantity(int $newQuantity): void
    {
        if ($newQuantity < 1) {
            throw new InvalidQuantityException($newQuantity);
        }

        $this->quantity = $newQuantity;
    }

    public function incrementQuantity(int $quantity): void
    {
        $this->setQuantity($this->quantity + $quantity);
    }
}