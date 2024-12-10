<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;

readonly class CheckoutCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId): void
    {
        $cart = $this->cartRepository->findByIdOrFail($cartId);
        $cart->checkout();
        $this->cartRepository->save($cart);
    }
}
