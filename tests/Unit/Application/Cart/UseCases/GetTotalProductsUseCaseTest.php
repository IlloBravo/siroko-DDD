<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use DateMalformedStringException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class GetTotalProductsUseCaseTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function testExecuteReturnsTotalProducts(): void
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

        $useCase = new GetTotalProductsUseCase($cartRepository);
        $totalProducts = $useCase->execute($cart->id->__toString());

        $this->assertEquals(3, $totalProducts);
    }
}
