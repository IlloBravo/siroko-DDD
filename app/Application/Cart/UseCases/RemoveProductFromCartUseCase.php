<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class RemoveProductFromCartUseCase
{
    public function __construct(private CartRepositoryInterface $cartRepository) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, string $productId): void
    {
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));
        $cart->removeProduct($productId);
        $this->cartRepository->save($cart);
    }
}
