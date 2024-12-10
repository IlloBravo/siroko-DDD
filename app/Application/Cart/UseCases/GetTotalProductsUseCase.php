<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;

readonly class GetTotalProductsUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId): int
    {
        $cart = $this->cartRepository->findByIdOrFail($cartId);
        return $cart->getTotalProducts();
    }
}
