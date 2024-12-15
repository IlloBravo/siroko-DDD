<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class GetTotalProductsUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId): int
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        return count($cart->getCartItems());
    }
}
