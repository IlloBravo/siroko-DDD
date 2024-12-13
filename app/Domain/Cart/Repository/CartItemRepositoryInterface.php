<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Shared\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObjects\UuidVO;

interface CartItemRepositoryInterface
{
    /**
     * @throws CartItemNotFoundException
     */
    public function findByIdOrFail(UuidVO $id): CartItem;
    /**
     * @throws InvalidQuantityException
     */
    public function create(UuidVO $cartId, UuidVO $productId, int $quantity): CartItem;
    /**
     * @throws InvalidQuantityException
     */
    public function updateQuantity(UuidVO $cartItemId, int $newQuantity): void;
    public function save(CartItem $cartItem): void;
    public function delete(UuidVO $id): void;
}