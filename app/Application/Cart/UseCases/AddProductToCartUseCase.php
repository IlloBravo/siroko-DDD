<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Cart\CartItem;

readonly class AddProductToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function execute(string $cartId, CartItem $cartItem): void
    {
        $cart = $this->cartRepository->findByIdOrFail($cartId);
        $cart->addCartItem($cartItem);
        $this->cartRepository->save($cart);
    }
}
