<?php

namespace App\Domain\Cart\Exceptions;

use RuntimeException;

class CartNotFoundException extends RuntimeException
{
    public function __construct(string $cartId)
    {
        $message = __('cart.cart_not_found', ['id' => $cartId]);
        parent::__construct($message);
    }
}
