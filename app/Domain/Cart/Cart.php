<?php

namespace App\Domain\Cart;

use App\Domain\Product\Product;
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

    public function addProduct(Product $product): void
    {
        $this->items->push($product);
        $this->updatedAt = new DateTime();
    }

    public function updateProductQuantity(string $productId, int $quantity): void
    {
        $this->items = $this->items->map(function ($item) use ($productId, $quantity) {
            if ($item->id === $productId) {
                $item->quantity = $quantity;
            }
            return $item;
        });
        $this->updatedAt = new DateTime();
    }

    public function removeProduct(string $productId): void
    {
        $this->items = $this->items->reject(fn($item): bool => $item->id === $productId);
        $this->updatedAt = new DateTime();
    }

    public function getTotalProducts(): int
    {
        return $this->items->sum(fn($item) => $item->quantity);
    }

    public function checkout(): void
    {
        $this->items = collect();
        $this->updatedAt = new DateTime();
    }
}
