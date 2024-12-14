<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class UpdateCartItemQuantityUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private CartItemRepositoryInterface $cartItemRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @throws CartNotFoundException
     * @throws InsufficientStockException
     */
    public function execute(string $cartId, string $cartItemId, int $newQuantity): void
    {
        if ($newQuantity <= 0) {
            throw new InvalidQuantityException($newQuantity);
        }

        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $cartItem = $cart->cartItems
            ->first(fn(CartItem $item) => $item->id->equals(UuidVO::fromString($cartItemId)));

        $product = $cartItem->product();

        $quantityDifference = $newQuantity - $cartItem->quantity;

        $updatedStock = $product->stock - $quantityDifference;

        if ($quantityDifference > 0 && $updatedStock < 0) {
            throw new InsufficientStockException($product->name, $product->stock);
        }

        $product->stock = $updatedStock;
        $this->productRepository->save($product);

        $cart->updateCartItemQuantity(
            $cartItem,
            $newQuantity
        );

        $this->cartItemRepository->save($cartItem);
        $this->cartRepository->save($cart);
    }
}
