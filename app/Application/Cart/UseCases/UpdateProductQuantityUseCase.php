<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use Exception;

readonly class UpdateProductQuantityUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws Exception
     */
    public function execute(string $cartId, string $productId, int $quantity): void
    {
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw new Exception("Cart not found.");
        }

        $cart->updateProductQuantity($productId, $quantity);
        $this->cartRepository->save($cart);
    }
}