<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Cart\CartItem;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class AddProductToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {}

    public function execute(string $cartId, CartItem $cartItem): void
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $cart->addCartItem($cartItem);
        $this->cartRepository->save($cart);
    }
}
