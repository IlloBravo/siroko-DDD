<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class UpdateCartItemQuantityUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private CartItemRepositoryInterface $cartItemRepository
    ) {}

    /**
     * @throws CartNotFoundException
     * @throws InsufficientStockException
     */
    public function execute(string $cartId, string $productId, int $newQuantity): void
    {
        if ($newQuantity <= 0) {
            throw new InvalidQuantityException($newQuantity);
        }

        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $cart->updateProductQuantity(
            UuidVO::fromString($productId),
            $newQuantity,
            $this->cartItemRepository
        );

        $this->cartRepository->save($cart);
    }
}
