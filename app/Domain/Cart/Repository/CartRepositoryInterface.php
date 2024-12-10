<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Shared\ValueObjects\UuidVO;

interface CartRepositoryInterface
{
    /**
     * @throws CartNotFoundException
     */
    public function findByIdOrFail(UuidVO $id): Cart;
    public function save(Cart $cart): void;
    public function delete(string $id): void;
}
