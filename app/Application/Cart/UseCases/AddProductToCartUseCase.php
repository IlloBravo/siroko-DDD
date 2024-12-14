<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Exceptions\ProductNotFoundException;
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
     * @throws ProductNotFoundException
     * @throws InsufficientStockException
     */
    public function execute(string $cartId, string $productId, int $quantity): void
    {
        $product = $this->productRepository->findByIdOrFail(UuidVO::fromString($productId));

        if (!$product->hasSufficientStock($quantity)) {
            throw new InsufficientStockException($productId);
        }

        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $cartItem = $this->cartItemRepository->create(
            $cart->id,
            $product->id,
            $quantity
        );

        $this->cartItemRepository->save($cartItem);;
        $product->decreaseStock($quantity);
        $this->productRepository->save($product);

        $cart->addProduct($cartItem, $this->cartItemRepository);
        $this->cartRepository->save($cart);
    }
}