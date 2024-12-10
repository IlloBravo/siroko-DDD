<?php

namespace App\Domain\Cart;

use App\Domain\Product\Product;
use DateTime;
use Illuminate\Support\Collection;

class Cart
{
    public function __construct(
        public string $id,
        public Collection $items,
        public DateTime $createdAt,
        public DateTime $updatedAt
    ) {}

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
        $this->items = $this->items->reject(fn($item) => $item->id === $productId);
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
