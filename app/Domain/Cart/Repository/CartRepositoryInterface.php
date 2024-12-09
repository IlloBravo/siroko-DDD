<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\Cart;

interface CartRepositoryInterface
{
    public function save(Cart $cart): void;
    public function findById(string $id): ?Cart;
    public function delete(string $id): void;
}
