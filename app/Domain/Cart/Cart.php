<?php

namespace App\Domain\Cart;

use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateTime;
use Illuminate\Support\Collection;

final class Cart
{
    public function __construct(
        public UuidVO     $id,
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

    public function addProduct(Product $product, int $quantity): void
    {
        $existingProduct = $this->products->first(fn(Product $item) => $item->id->equals($product->id));

        if ($product->stock < $quantity) {
            throw new InsufficientStockException((string) $product->id);
        }

        if ($existingProduct) {
            $existingProduct->cartQuantity += $quantity;
        } else {
            $product->cartQuantity = $quantity;
            $this->products->push($product);
        }

        $product->stock -= $quantity;
    }


    public function updateProductQuantity(UuidVO $productId, int $newQuantity): void
    {
        $this->products->each(function (Product $product) use ($productId, $newQuantity) {
            if ($product->id->equals($productId)) {
                $difference = $newQuantity - $product->cartQuantity;

                if ($difference > 0) {
                    $product->decreaseStock($difference);
                } elseif ($difference < 0) {
                    $product->increaseStock(abs($difference));
                }

                $product->cartQuantity = $newQuantity;
            }
        });

    }

    public function removeProduct(UuidVO $productId): void
    {
        $this->products = $this->products->reject(function (Product $product) use ($productId) {
            if ($product->id->equals($productId)) {
                $product->cartQuantity = 0;
                $product->increaseStock($product->cartQuantity);
            }
        });

    }

    public function getTotalProducts(): int
    {
        return $this->products->sum(fn(Product $product) => $product->cartQuantity);
    }

    public function checkout(): void
    {
        $this->products = collect();
    }

    public function getProductStock(UuidVO $productId): int
    {
        $product = $this->getProduct($productId);

        if (!$product) {
            return 0;
        }

        return $product->stock + $product->cartQuantity;
    }

    public function getProduct(UuidVO $productId): ?Product
    {
        return $this->products->first(fn(Product $product) => $product->id->equals($productId));
    }

    public function getProductQuantity(UuidVO $productId): int
    {
        $product = $this->products
            ->filter(fn(Product $product) => $product->id->equals($productId))
            ->first();

        return $product ? $product->cartQuantity : 0;
    }
}
