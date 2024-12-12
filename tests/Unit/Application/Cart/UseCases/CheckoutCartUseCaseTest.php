<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\CheckoutCartUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use DateMalformedStringException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CheckoutCartUseCaseTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function testExecuteCheckoutCart(): void
    {
        $cart = Cart::create([
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productData = (object) [
            'id' => '123e4567-e89b-12d3-a456-426614174001',
            'name' => 'Producto B',
            'price' => 30.0,
            'quantity' => 20,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cart->addProduct($product, 3);

        $cartRepository = $this->createMock(CartRepositoryInterface::class);
        $cartRepository->expects($this->once())
            ->method('findByIdOrFail')
            ->with($cart->id)
            ->willReturn($cart);
        $cartRepository->expects($this->once())
            ->method('save')
            ->with($cart);

        $useCase = new CheckoutCartUseCase($cartRepository);
        $useCase->execute($cart->id->__toString());

        $this->assertEmpty($cart->items);
    }
}
