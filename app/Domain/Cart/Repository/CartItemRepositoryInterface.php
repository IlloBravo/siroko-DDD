<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\CartItem;
use App\Domain\Shared\ValueObjects\UuidVO;

interface CartItemRepositoryInterface
{
    public function findByIdOrFail(UuidVO $id): CartItem;
    public function save(CartItem $cartItem): void;
    public function delete(UuidVO $id): void;
}