<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RemoveProductFromCartUseCaseTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function testExecute(): void
    {
        $cartId = Uuid::uuid4()->toString();
        $productId = Uuid::uuid4()->toString();

        $cart = Cart::create([
            'id' => Uuid::uuid4(),
            'items' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product = Product::fromDatabase((object)[
            'id' => $productId,
            'name' => 'Producto de Prueba',
            'price' => 20.0,
            'quantity' => 10,
            'cartQuantity' => 0,
        ]);
        $cart->addProduct($product, 3);

        $mockCartRepository = $this->createMock(CartRepositoryInterface::class);
        $mockCartRepository->expects($this->once())
            ->method('findByIdOrFail')
            ->with($this->callback(function ($uuid) use ($cartId) {
                return $uuid->__toString() === $cartId;
            }))
            ->willReturn($cart);
        $mockCartRepository->expects($this->once())->method('save')->with($cart);

        $mockProductRepository = $this->createMock(ProductRepositoryInterface::class);
        $mockProductRepository->expects($this->once())
            ->method('increaseStock')
            ->with($this->callback(function ($uuid) use ($productId) {
                return $uuid->__toString() === $productId;
            }), 3);

        $useCase = new RemoveProductFromCartUseCase($mockCartRepository, $mockProductRepository);
        $useCase->execute($cartId, $productId);

        $this->assertEquals(0, $cart->getProductQuantity(UuidVO::fromString($productId)));
    }
}
