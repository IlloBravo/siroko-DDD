<?php

namespace App\Domain\Cart\Exceptions;

use RuntimeException;

class CartNotFoundException extends RuntimeException
{
    public function __construct(string $cartId)
    {
        parent::__construct("Cart with ID {$cartId} not found.");
    }
}
