<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;

readonly class RemoveProductFromCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, string $productId): void
    {
        $cart = $this->cartRepository->findByIdOrFail($cartId);
        $cart->removeProduct($productId);
        $this->cartRepository->save($cart);
    }
}
