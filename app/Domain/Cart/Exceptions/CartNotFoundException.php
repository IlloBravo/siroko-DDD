<?php

namespace App\Domain\Cart\Exceptions;

use RuntimeException;
use Illuminate\Support\Facades\Lang;

class CartNotFoundException extends RuntimeException
{
    public function __construct(string $cartId)
    {
        $message = Lang::get('Cart.cart_not_found', ['id' => $cartId]);
        parent::__construct($message);
    }
}
