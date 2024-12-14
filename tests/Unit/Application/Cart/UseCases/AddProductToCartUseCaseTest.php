<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\AddProductToCartUseCase;
use App\Domain\Shared\ValueObjects\UuidVO;
use Tests\TestCase;
use Tests\Traits\RepositoryMockTrait;

class AddProductToCartUseCaseTest extends TestCase
{
    use RepositoryMockTrait;

    public function testAddProductToCartSuccessfully(): void
    {
        $cartData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'items' => json_encode([]),
        ];

        $productData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $cartItemData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'cart' => $cartData,
            'product' => $productData,
            'quantity' => 3,
        ];

        $this->mockRepositories($productData, $cartData, $cartItemData);

        $useCase = app(AddProductToCartUseCase::class);

        $useCase->execute($cartData->id, $productData->id, 3);

        $this->assertTrue(true);
    }
}