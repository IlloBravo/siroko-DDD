<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;

interface CartRepositoryInterface
{
    /**
     * @throws CartNotFoundException
     */
    public function findByIdOrFail(string $id): Cart;

    public function save(Cart $cart): void;
    public function delete(string $id): void;
}
