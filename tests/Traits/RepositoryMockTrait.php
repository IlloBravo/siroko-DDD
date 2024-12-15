<?php

namespace Tests\Traits;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Mockery;

trait RepositoryMockTrait
{
    protected function mockRepositories($productData, $cartData, $itemData): void
    {
        $productRepoMock = Mockery::mock(ProductRepositoryInterface::class);
        $productRepoMock
            ->shouldReceive('findByIdOrFail')
            ->andReturn(Product::fromDatabase($productData));
        $productRepoMock
            ->shouldReceive('save')
            ->andReturn();

        $cartRepoMock = Mockery::mock(CartRepositoryInterface::class);
        $cartRepoMock
            ->shouldReceive('findByIdOrFail')
            ->andReturn(Cart::fromDatabase($cartData));
        $cartRepoMock
            ->shouldReceive('save')
            ->andReturn();

        $cartItemRepoMock = Mockery::mock(CartItemRepositoryInterface::class);
        $cartItemRepoMock
            ->shouldReceive('create')
            ->andReturn(CartItem::fromDatabase($itemData));
        $cartItemRepoMock
            ->shouldReceive('save')
            ->andReturn();

        $this->app->instance(ProductRepositoryInterface::class, $productRepoMock);
        $this->app->instance(CartRepositoryInterface::class, $cartRepoMock);
        $this->app->instance(CartItemRepositoryInterface::class, $cartItemRepoMock);
    }

    protected function mockProductRepositoryFailure(string $productId): void
    {
        $productRepoMock = \Mockery::mock(\App\Domain\Product\Repository\ProductRepositoryInterface::class);

        // Crear UuidVO a partir del string recibido
        $productUuidVO = new \App\Domain\Shared\ValueObjects\UuidVO($productId);

        // Simular que se lanza ProductNotFoundException con el UuidVO correcto
        $productRepoMock
            ->shouldReceive('findByIdOrFail')
            ->with($productUuidVO) // Asegurarnos de que reciba un objeto UuidVO
            ->andThrow(new \App\Domain\Product\Exceptions\ProductNotFoundException($productId));

        // Bind del mock al contenedor de Laravel
        $this->app->instance(\App\Domain\Product\Repository\ProductRepositoryInterface::class, $productRepoMock);
    }
}