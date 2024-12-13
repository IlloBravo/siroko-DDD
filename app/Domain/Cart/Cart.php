<?php

namespace App\Domain\Cart;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;

final class Cart
{
    public function __construct(
        public UuidVO     $id,
        public Collection $cartItems
    ) {}

    public static function fromDatabase(object $data): self
    {
        $cartItemIds = json_decode($data->items, true);
        return new self(
            UuidVO::fromString($data->id),
            collect($cartItemIds)
        );
    }

    public function addProduct(CartItem $newCartItem): void
    {
        $existingCartItem = $this->cartItems
            ->first(fn(CartItem $item) => $item->productId->equals($newCartItem->productId));

        if ($existingCartItem) {
            $existingCartItem->incrementQuantity($newCartItem->quantity);
        } else {
            $this->cartItems->push($newCartItem);
        }
    }

    public function updateProductQuantity(UuidVO $productId, int $newQuantity): void
    {
        $cartItem = $this->cartItems
            ->first(fn(CartItem $item) => $item->productId->equals($productId));

        if (!$cartItem) {
            throw new CartNotFoundException($this->id);
        }

        if ($newQuantity < 1) {
            throw new InvalidQuantityException($newQuantity);
        }

        $cartItem->setQuantity($newQuantity);
    }

    public function removeProduct(UuidVO $productId): void
    {
        $this->cartItems = $this->cartItems
            ->reject(fn(CartItem $item) => $item->productId->equals($productId));
    }

    public function checkout(): void
    {
        $this->cartItems = collect();
    }

    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function total(): float
    {
        return $this->cartItems->sum(
            fn(CartItem $item) => $item->product->price * $item->quantity
        );
    }
}
