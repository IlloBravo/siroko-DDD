<?php

namespace Tests\Unit\Application\Cart\UseCases;

use App\Application\Cart\UseCases\GetTotalProductsUseCase;
use App\Domain\Cart\Cart;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GetTotalProductsUseCaseTest extends TestCase
{
    public function testExecuteReturnsTotalProducts(): void
    {
        $product_id = UuidVO::generate()->__toString();
        DB::table('products')->insert([
            'id' => $product_id,
            'name' => 'Bike',
            'price' => 1500.00,
            'stock' => 10,
        ]);

        $cart_id = UuidVO::generate()->__toString();
        DB::table('carts')->insert([
            'id' => $cart_id,
            'items' => json_encode([]), // Inicialmente vacío
        ]);

        $cart_item_id = UuidVO::generate()->__toString();
        DB::table('cart_items')->insert([
            'id' => $cart_item_id,
            'cart_id' => $cart_id,
            'product_id' => $product_id,
            'quantity' => 3,
        ]);

        DB::table('carts')
            ->where('id', $cart_id)
            ->update([
                'items' => json_encode([
                    [
                        'id' => $cart_item_id,
                        'cart_id' => $cart_id,
                        'product_id' => $product_id,
                        'quantity' => 3,
                    ],
                ]),
            ]);

        $cartRow = DB::table('carts')->where('id', $cart_id)->first();
        $cart = Cart::fromDatabase($cartRow);

        $useCase = app(GetTotalProductsUseCase::class);
        $totalProducts = $useCase->execute($cart->id->__toString());

        $this->assertEquals(1, $totalProducts);
    }
}