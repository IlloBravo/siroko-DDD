<?php

namespace Domain\Cart\Exceptions;

use App\Domain\Cart\Exceptions\CartItemNotFoundException;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class CartItemNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage()
    {
        Lang::shouldReceive('get')
            ->once()
            ->with('Cart.cart_item_not_found', ['id' => 'some-cart-id'])
            ->andReturn('Cart Item with ID some-cart-id not found');

        $this->expectException(CartItemNotFoundException::class);
        $this->expectExceptionMessage('Cart Item with ID some-cart-id not found');

        throw new CartItemNotFoundException('some-cart-id');
    }
}
