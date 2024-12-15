<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\RemoveProductFromCartUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Traits\RepositoryMockTrait;

class RemoveProductFromCartUseCaseTest extends TestCase
{
    use RepositoryMockTrait;
    public function testExecute(): void
    {
        // Configuramos los datos del carrito, producto, y item del carrito
        $cartData = (object) [
            'id' => UuidVO::generate()->__toString(),
            'items' => json_encode([]), // Inicialmente vacío
        ];

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

        // Insertamos los datos necesarios en la base de datos para cumplir restricciones de claves foráneas
        DB::table('carts')->insert([
            'id' => $cartData->id,
            'items' => $cartData->items,
        ]);

        DB::table('products')->insert([
            'id' => $productData->id,
            'name' => $productData->name,
            'price' => $productData->price,
            'stock' => $productData->stock,
        ]);

        DB::table('cart_items')->insert([
            'id' => $cartItemData->id,
            'cart_id' => $cartItemData->cart_id,
            'product_id' => $cartItemData->product_id,
            'quantity' => $cartItemData->quantity,
        ]);

        DB::table('carts')
            ->where('id', $cartData->id)
            ->update([
                'items' => json_encode([
                    [
                        'id' => $cartItemData->id,
                        'cart_id' => $cartData->id,
                        'product_id' => $productData->id,
                        'quantity' => 3,
                    ],
                ]),
            ]);

        // Creamos la entidad de Cart desde la base de datos
        $cart = Cart::fromDatabase($cartData);

        $cartItem = CartItem::fromDatabase($cartItemData);

        // Ejecutamos el caso de uso
        $useCase = app(RemoveProductFromCartUseCase::class);
        $useCase->execute($cart->id, $cartItem->id);

        // Verificamos que se ha eliminado el ítem del carrito
        $this->assertCount(0, $cart->getCartItems());
    }
}
