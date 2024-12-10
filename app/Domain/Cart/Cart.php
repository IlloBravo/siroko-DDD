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
            collect(json_decode($data['items'], true))->map(
                fn($item) => Product::fromDatabase((object) $item)
            ),
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
            collect(json_decode($data->items, true))->map(
                fn($item) => Product::fromDatabase((object) $item)
            ),
            new DateTime($data->created_at),
            new DateTime($data->updated_at)
        );
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $productWithQuantity = clone $product;
        $productWithQuantity->quantity = $quantity;

        $this->items->push($productWithQuantity);
        $this->updatedAt = new DateTime();
    }

    public function updateProductQuantity(UuidVO $productId, int $quantity): void
    {
        $this->items = $this->items->map(function (Product $item) use ($productId, $quantity) {
            if ($item->id->equals($productId)) {
                $item->quantity = $quantity;
            }
            return $item;
        });
        $this->updatedAt = new DateTime();
    }

    public function removeProduct(UuidVO $productId): void
    {
        $this->items = $this->items->reject(fn(Product $item) => $item->id->equals($productId));
        $this->updatedAt = new DateTime();
    }

    public function getTotalProducts(): int
    {
        return $this->items->sum(fn(Product $item) => $item->quantity);
    }

    public function checkout(): void
    {
        $this->items = collect();
        $this->updatedAt = new DateTime();
    }
}
