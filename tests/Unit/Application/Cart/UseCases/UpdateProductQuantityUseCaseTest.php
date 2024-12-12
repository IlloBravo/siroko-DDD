<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\UpdateProductQuantityUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use DateMalformedStringException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateProductQuantityUseCaseTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function testExecute()
    {
        $cartData = [
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $cart = Cart::create($cartData);

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
        $productRepository = $this->createMock(ProductRepositoryInterface::class);

        $cartRepository->expects($this->once())
            ->method('findByIdOrFail')
            ->with($cart->id)
            ->willReturn($cart);
        $cartRepository->expects($this->once())->method('save')->with($cart);

        $productRepository->expects($this->once())->method('save')->with($product);

        $useCase = new UpdateProductQuantityUseCase($cartRepository, $productRepository);
        $useCase->execute($cart->id->__toString(), $product->id, 5);

        $this->assertEquals(5, $cart->getProductQuantity(UuidVO::fromString($product->id)));
    }
}
