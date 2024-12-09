<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use Exception;

readonly class GetTotalProductsUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws Exception
     */
    public function execute(string $cartId): int
    {
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart) {
            throw new Exception("Cart not found.");
        }

        return $cart->getTotalProducts();
    }
}