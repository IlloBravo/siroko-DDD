<?php

namespace Domain\Cart\Exceptions;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class CartNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage()
    {
        Lang::shouldReceive('get')
            ->once()
            ->with('Cart.cart_not_found', ['id' => 'some-cart-id'])
            ->andReturn('Cart not found for ID some-cart-id');

        $this->expectException(CartNotFoundException::class);
        $this->expectExceptionMessage('Cart not found for ID some-cart-id');

        throw new CartNotFoundException('some-cart-id');
    }
}
