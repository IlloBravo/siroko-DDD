<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use Exception;

readonly class RemoveProductFromCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws Exception
     */
    public function execute(string $cartId, string $productId): void
    {
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw new Exception("Cart not found.");
        }

        $cart->removeProduct($productId);
        $this->cartRepository->save($cart);
    }
}
