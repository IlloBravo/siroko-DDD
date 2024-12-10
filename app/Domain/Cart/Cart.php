<?php

namespace App\Domain\Cart;

use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use DateTime;
use Illuminate\Support\Collection;

final class Cart
{
    public function __construct(
        public UuidVO $id,
        public Collection $items,
        public DateTime $createdAt,
        public DateTime $updatedAt
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public static function create(array $data): self
    {
        return new self(
            UuidVO::fromString($data['id']),
            collect(json_decode($data['items'], true)),
            new DateTime($data['created_at']),
            new DateTime($data['updated_at'])
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function fromDatabase(object $data): self
    {
        return new self(
            UuidVO::fromString($data->id),
            collect(json_decode($data->items, true)),
            new DateTime($data->created_at),
            new DateTime($data->updated_at)
        );
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $this->items->push([
            'product' => $product,
            'quantity' => $quantity,
        ]);
        $this->updatedAt = new DateTime();
    }

    public function updateProductQuantity(string $productId, int $quantity): void
    {
        $this->items = $this->items->map(function ($item) use ($productId, $quantity) {
            if ($item['product']->id === $productId) {
                $item['quantity'] = $quantity;
            }
            return $item;
        });
        $this->updatedAt = new DateTime();
    }

    public function removeProduct(string $productId): void
    {
        $this->items = $this->items->reject(fn($item) => $item['product']->id === $productId);
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
