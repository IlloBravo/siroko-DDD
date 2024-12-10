<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;

readonly class AddProductToCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, Product $product): void
    {
        $cart = $this->cartRepository->findByIdOrFail($cartId);
        $cart->addProduct($product);
        $this->cartRepository->save($cart);
    }
}
