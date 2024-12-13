<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function findByIdOrFail(UuidVO $id): Cart
    {
        $cartData = DB::table('carts')->where('id', (string) $id)->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        return Cart::fromDatabase($cartData);
    }

    public function findAll(): Collection
    {
        $cartsData = DB::table('carts')->get();

        return $cartsData->map(function ($cartData) {
            return Cart::fromDatabase($cartData);
        });
    }

    public function save(Cart $cart): void
    {
        $product = DB::table('products')->where(
            'id', (string) $cart->cartItems->first()->productId
        )->first();

        DB::table('carts')->updateOrInsert(
            ['id' => (string) $cart->id],
            [
                'items' => json_encode(
                    $cart->cartItems->map(fn(CartItem $item) => [
                        'id' => (string) $item->id,
                        'product' => [
                            'id' => (string) $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                        ],
                        'quantity' => $item->quantity,
                    ])->toArray()
                ),
            ]
        );
    }

    public function delete(UuidVO $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
