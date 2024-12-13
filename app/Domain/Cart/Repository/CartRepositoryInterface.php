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

    /**
     * Retrieve all carts.
     *
     * @return Collection<int, Cart>
     */
    public function findAll(): Collection;

    /**
     * Save a cart (insert or update its data and relationships).
     *
     * @param Cart $cart
     */
    public function save(Cart $cart): void;

    /**
     * Delete a cart by its ID.
     *
     * @param UuidVO $id
     */
    public function delete(UuidVO $id): void;
}