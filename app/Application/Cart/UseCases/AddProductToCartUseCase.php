<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class AddProductToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private CartItemRepositoryInterface $cartItemRepository
    ) {}

    /**
     * @throws CartNotFoundException
     * @throws InsufficientStockException
     */
    public function execute(string $cartId, string $productId, int $quantity): void
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $product = $this->productRepository->findByIdOrFail(UuidVO::fromString($productId));

        $cartItem = CartItem::create($cart, $product, $quantity);
        $this->cartItemRepository->save($cartItem);

        $cart->addProduct($cartItem);

        $product->decreaseStock($quantity);

        $this->cartRepository->save($cart);
        $this->productRepository->save($product);
    }
}