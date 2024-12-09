<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use Exception;

readonly class AddProductToCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws Exception
     */
    public function execute(string $cartId, Product $product): void
    {
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw new Exception("Cart not found.");
        }

        $cart->addProduct($product);
        $this->cartRepository->save($cart);
    }
}
