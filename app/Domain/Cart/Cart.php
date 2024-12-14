<?php

namespace App\Domain\Cart;

use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;

final class Cart
{
    public function __construct(
        public UuidVO     $id,
        public Collection $cartItems
    ) {}

    public static function fromDatabase(object $data): self
    {
        $cartItems = collect(json_decode($data->items, true))->map(function ($item) {
            return new CartItem(
                UuidVO::fromString($item['id']),
                UuidVO::fromString($item['cartId']),
                UuidVO::fromString($item['productId']),
                $item['quantity']
            );
        });

        return new self(
            UuidVO::fromString($data->id),
            $cartItems
        );
    }

    public function addCartItem(CartItem $newCartItem, int $quantity): void
    {
        $existingCartItem = $this->cartItems
            ->first(fn(CartItem $item) => $item->productId->equals($newCartItem->productId));

        if ($existingCartItem) {
            $existingCartItem->quantity += $quantity;
        } else {
            $this->cartItems->push($newCartItem);
        }
    }

    public function updateCartItemQuantity(CartItem $cartItemId, int $newQuantity): void
    {
        $cartItem = $this->cartItems
            ->first(fn(CartItem $item) => $item->id->equals($cartItemId->id));

        $cartItem->quantity = $newQuantity;
    }

    public function removeCartItem(UuidVO $cartItemId): void
    {
        $this->cartItems = $this->cartItems
            ->reject(fn(CartItem $item) => $item->id->equals($cartItemId));
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
            fn(CartItem $item) => $item->product()->price * $item->quantity
        );
    }
}
