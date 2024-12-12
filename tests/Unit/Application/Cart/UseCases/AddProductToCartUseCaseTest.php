<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use DateMalformedStringException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AddProductToCartUseCaseTest extends TestCase
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
            'name' => 'Producto A',
            'price' => 50.0,
            'quantity' => 10,
            'cartQuantity' => 0,
        ];
        $product = Product::fromDatabase($productData);

        $cartRepository = $this->createMock(CartRepositoryInterface::class);
        $productRepository = $this->createMock(ProductRepositoryInterface::class);

        $cartRepository->expects($this->once())->method('save')->with($cart);
        $productRepository->expects($this->once())->method('updateStock')->with($product->id, 5);

        $useCase = new AddProductToCartUseCase($cartRepository, $productRepository);
        $useCase->execute($cart, $product, 5);

        $this->assertEquals(5, $cart->getProductQuantity(UuidVO::fromString($product->id)));
        $this->assertEquals(5, $product->quantity);
    }
}
