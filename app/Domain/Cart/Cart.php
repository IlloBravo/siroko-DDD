<?php

namespace App\Domain\Cart;

use DateMalformedStringException;
use DateTime;
use Illuminate\Support\Collection;

final class Cart
{
    public function __construct(
        public readonly string $id,
        public Collection $items,
        public readonly DateTime $createdAt,
        public DateTime $updatedAt
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public static function fromArray(string $id, Collection $items, string $createdAt, string $updatedAt): self
    {
        return new self(
            $id,
            $items,
            new DateTime($createdAt),
            new DateTime($updatedAt)
        );
    }

    public function addCartItem(CartItem $cartItem): void
    {
        $this->items->push($cartItem);
        $this->updatedAt = new DateTime();
    }

    public function updateProductQuantity(string $productId, int $quantity): void
    {
        $this->items = $this->items->map(function ($cartItem) use ($productId, $quantity) {
            if ($cartItem->product->id === $productId) {
                $cartItem->updateQuantity($quantity);
            }
            return $cartItem;
        });
        $this->updatedAt = new DateTime();
    }

    public function removeProduct(string $productId): void
    {
        $this->items = $this->items->reject(fn($cartItem): bool => $cartItem->product->id === $productId);
        $this->updatedAt = new DateTime();
    }

    public function getTotalProducts(): int
    {
        return $this->items->sum(fn($cartItem) => $cartItem->quantity);
    }

    public function checkout(): void
    {
        $this->items = collect();
        $this->updatedAt = new DateTime();
    }
}
