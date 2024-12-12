<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;

readonly class AddProductToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(Cart $cart, Product $product, int $quantity): void
    {
        $cart->addProduct($product, $quantity);
        $this->cartRepository->save($cart);
        $this->productRepository->save($product);
    }
}
