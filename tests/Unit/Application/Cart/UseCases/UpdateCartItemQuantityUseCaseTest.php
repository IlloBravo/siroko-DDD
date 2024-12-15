<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\UpdateCartItemQuantityUseCase;
use App\Domain\Shared\ValueObjects\UuidVO;
use Tests\TestCase;
use Tests\Traits\RepositoryMockTrait;

class UpdateCartItemQuantityUseCaseTest extends TestCase
{
    use RepositoryMockTrait;

    public function testUpdateCartItemQuantitySuccessfully(): void
    {
        // Datos iniciales simulados
        $cartId = UuidVO::generate()->__toString();
        $cartItemId = UuidVO::generate()->__toString();
        $productId = UuidVO::generate()->__toString();

        $cartData = (object) [
            'id' => $cartId,
            'items' => json_encode([
                [
                    'id' => $cartItemId,
                    'cart_id' => $cartId,
                    'product_id' => $productId,
                    'quantity' => 3, // Cantidad inicial
                ],
            ]),
        ];

        $productData = (object) [
            'id' => $productId,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $cartItemData = (object) [
            'id' => $cartItemId,
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => 3,
        ];

        // Mock de los repositorios con los datos simulados
        $this->mockRepositories($productData, $cartData, $cartItemData);

        // Creamos la instancia del caso de uso a probar
        $useCase = app(UpdateCartItemQuantityUseCase::class);

        // Nueva cantidad para actualizar
        $newQuantity = 5;

        // Ejecutamos el caso de uso
        $useCase->execute($cartId, $cartItemId, $newQuantity);

        // Comprobamos que la prueba pasó correctamente (sin excepciones)
        $this->assertTrue(true);
    }

    public function testUpdateCartItemQuantityIncreaseStockSuccessfully(): void
    {
        // Datos iniciales simulados
        $cartId = UuidVO::generate()->__toString();
        $cartItemId = UuidVO::generate()->__toString();
        $productId = UuidVO::generate()->__toString();

        $cartData = (object) [
            'id' => $cartId,
            'items' => json_encode([
                [
                    'id' => $cartItemId,
                    'cart_id' => $cartId,
                    'product_id' => $productId,
                    'quantity' => 5, // Cantidad inicial
                ],
            ]),
        ];

        $productData = (object) [
            'id' => $productId,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ];

        $cartItemData = (object) [
            'id' => $cartItemId,
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => 3,
        ];

        // Mock de los repositorios con los datos simulados
        $this->mockRepositories($productData, $cartData, $cartItemData);

        // Creamos la instancia del caso de uso a probar
        $useCase = app(UpdateCartItemQuantityUseCase::class);

        // Nueva cantidad para actualizar
        $newQuantity = 2;

        // Ejecutamos el caso de uso
        $useCase->execute($cartId, $cartItemId, $newQuantity);

        // Comprobamos que la prueba pasó correctamente (sin excepciones)
        $this->assertTrue(true);
    }
}