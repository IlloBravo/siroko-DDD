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
        /** @var Collection<int, CartItem> */
        public Collection $cartItems
    ) {}

    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            collect(json_decode($data->items, true))->map(
                fn($item) => CartItem::fromDatabase((object) $item)
            )
        );
    }

    public function addProduct(CartItem $newCartItem): void
    {
        $existingCartItem = $this->cartItems
            ->first(fn(CartItem $item) => $item->product->id->equals($newCartItem->product->id));

        if ($existingCartItem) {
            $existingCartItem->setQuantity($existingCartItem->quantity + $newCartItem->quantity);
        } else {
            $this->cartItems->push($newCartItem);
        }
    }

    public function updateProductQuantity(UuidVO $productId, int $newQuantity): void
    {
        $cartItem = $this->cartItems
            ->first(fn(CartItem $item) => $item->product->id->equals($productId));

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
            ->reject(fn(CartItem $item) => $item->product->id->equals($productId));
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
