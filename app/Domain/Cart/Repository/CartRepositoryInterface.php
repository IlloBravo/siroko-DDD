<?php

namespace App\Domain\Cart\Repository;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;

interface CartRepositoryInterface
{
    /**
     * @throws CartNotFoundException
     */
    public function findByIdOrFail(UuidVO $id): Cart;
    public function findAll(): Collection;
    public function save(Cart $cart): void;
    public function delete(UuidVO $id): void;
}
