<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;

readonly class UpdateProductQuantityUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, string $productId, int $quantity): void
    {
        $cart = $this->cartRepository->findByIdOrFail($cartId);
        $cart->updateProductQuantity($productId, $quantity);
        $this->cartRepository->save($cart);
    }
}
