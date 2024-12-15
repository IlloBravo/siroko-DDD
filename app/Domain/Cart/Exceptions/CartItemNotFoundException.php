<?php

namespace App\Domain\Cart\Exceptions;

use RuntimeException;
use Illuminate\Support\Facades\Lang;

final class CartItemNotFoundException extends RuntimeException
{
    public function __construct(string $cartItemId)
    {
        $message = Lang::get('Cart.cart_item_not_found', ['id' => $cartItemId]);
        parent::__construct($message);
    }
}
