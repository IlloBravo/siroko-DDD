<?php

namespace Tests\Unit\Domain\Product\Exceptions;

use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Lang;

class ProductExceptionTest extends TestCase
{
    public function testInsufficientStockException()
    {
        // Mock para el lenguaje, aseguramos que el mensaje se recupere de Lang correctamente
        Lang::shouldReceive('get')
            ->once()
            ->with('Product.insufficient_stock', ['id' => '123e4567-e89b-12d3-a456-426614174001'])
            ->andReturn('No hay suficiente stock para el producto con ID 123e4567-e89b-12d3-a456-426614174001');

        // Probar que la excepción se lanza correctamente
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('No hay suficiente stock para el producto con ID 123e4567-e89b-12d3-a456-426614174001');

        // Lanzar la excepción
        throw new InsufficientStockException('123e4567-e89b-12d3-a456-426614174001');
    }

    public function testProductNotFoundException()
    {
        // Mock para el lenguaje, aseguramos que el mensaje se recupere de Lang correctamente
        Lang::shouldReceive('get')
            ->once()
            ->with('Product.product_not_found', ['id' => '123e4567-e89b-12d3-a456-426614174001'])
            ->andReturn('Producto con ID 123e4567-e89b-12d3-a456-426614174001 no encontrado');

        // Probar que la excepción se lanza correctamente
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('Producto con ID 123e4567-e89b-12d3-a456-426614174001 no encontrado');

        // Lanzar la excepción
        throw new ProductNotFoundException('123e4567-e89b-12d3-a456-426614174001');
    }
}
