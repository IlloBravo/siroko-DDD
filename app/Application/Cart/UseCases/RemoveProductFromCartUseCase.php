<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class RemoveProductFromCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private CartItemRepositoryInterface $cartItemRepository
    ) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, string $cartItemId): void
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $cartItem = $cart->getCartItems()->first(
            fn (CartItem $item) => $item->id->equals(UuidVO::fromString($cartItemId))
        );

        if (!$cartItem) {
            throw new CartItemNotFoundException($cartItemId);
        }

        $product = $this->productRepository->findByIdOrFail($cartItem->productId);

        $product->increaseStock($cartItem->quantity);

        $cart->removeCartItem($cartItem->id);

        $this->cartRepository->save($cart);

        $this->productRepository->save($product);
        $this->cartItemRepository->delete($cartItem->id);
    }

}
