<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class AddProductToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @throws CartNotFoundException
     * @throws InsufficientStockException
     */
    public function execute(string $cartId, string $productId, int $quantity): void
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $product = $this->productRepository->findByIdOrFail(UuidVO::fromString($productId));

        $existingCartItem = $cart->getCartItems()->first(
            fn($item) => $item->product->id->equals($product->id)
        );

        if ($existingCartItem) {
            $existingCartItem->incrementQuantity($quantity);
        } else {
            $cartItem = CartItem::create($cart, $product, $quantity);
            $cart->addProduct($cartItem);
        }

        $product->decreaseStock($quantity);

        $this->cartRepository->save($cart);
        $this->productRepository->save($product);
    }
}