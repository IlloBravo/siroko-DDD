<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use App\Domain\Shared\ValueObjects\UuidVO;

interface CartItemRepositoryInterface
{
    /**
     * @throws CartItemNotFoundException
     */
    public function findByIdOrFail(UuidVO $id): CartItem;
    public function save(CartItem $cartItem): void;
    public function delete(UuidVO $id): void;
}