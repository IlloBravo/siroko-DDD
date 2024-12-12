<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class CheckoutCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId): void
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $cart->checkout();
        $this->cartRepository->save($cart);
    }
}
