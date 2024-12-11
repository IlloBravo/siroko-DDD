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
        $product->decreaseStock($quantity);

        $productInCart = clone $product;
        $productInCart->cartQuantity = $quantity;

        $this->items->push($productInCart);
        $this->updatedAt = new DateTime();
    }

    public function updateProductQuantity(UuidVO $productId, int $newQuantity): void
    {
        $this->items = $this->items->map(function (Product $item) use ($productId, $newQuantity) {
            if ($item->id->equals($productId)) {
                $difference = $newQuantity - $item->cartQuantity;

                if ($difference > 0) {
                    $item->decreaseStock($difference);
                } elseif ($difference < 0) {
                    $item->increaseStock(abs($difference));
                }

                $item->cartQuantity = $newQuantity;
            }
            return $item;
        });

        $this->updatedAt = new DateTime();
    }

    public function removeProduct(UuidVO $productId): void
    {
        $this->items = $this->items->reject(function (Product $item) use ($productId) {
            if ($item->id->equals($productId)) {
                $item->increaseStock($item->cartQuantity);
                return true;
            }
            return false;
        });

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

    public function getProductStock(UuidVO $productId): int
    {
        $product = $this->items->first(fn(Product $item) => $item->id->equals($productId));

        return $product->quantity;
    }

    public function getProduct(UuidVO $productId): ?Product
    {
        return $this->items->first(fn(Product $item) => $item->id->equals($productId));
    }
}
