<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Shared\ValueObjects\UuidVO;
use Tests\TestCase;
use Tests\Traits\RepositoryMockTrait;

class CheckoutCartUseCaseTest extends TestCase
{
    use RepositoryMockTrait;

    public function testExecuteCheckoutCart(): void
    {
        $cartData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'items' => json_encode([]),
        ];

        $cart = Cart::fromDatabase($cartData);

        $productData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $cartItemData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cart_id' => $cartData->id,
            'product_id' => $productData->id,
            'quantity' => 3,
        ];

        $this->mockRepositories($productData, $cartData, $cartItemData);

        $useCase = app(CheckoutCartUseCase::class);

        $useCase->execute($cartData->id);

        $this->assertEmpty($cart->getCartItems());
    }
}
