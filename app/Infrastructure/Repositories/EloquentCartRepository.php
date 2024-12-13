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
        $cartData = DB::table('carts')
            ->where('id', (string) $id)
            ->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        $cartItemsData = json_decode($cartData->items ?? '[]', true);

        $cartItems = array_map(function ($item) {
            $productData = DB::table('products')->where('id', $item['product']['id'])->first();

            return CartItem::fromDatabase((object) [
                'id' => $item['id'],
                'cart' => null,
                'product' => $productData,
                'quantity' => $item['quantity'],
            ]);
        }, $cartItemsData);

        $cart = Cart::fromDatabase((object) [
            'id' => $cartData->id,
            'items' => json_encode($cartItems),
        ]);

        foreach ($cart->getCartItems() as $cartItem) {
            $cartItem->cart = $cart;
        }

        return $cart;
    }

    public function findAll(): Collection
    {
        $cartsData = DB::table('carts')->get();

        return $cartsData->map(fn($cartData) => Cart::fromDatabase($cartData));
    }

    public function save(Cart $cart): void
    {
        DB::table('carts')->updateOrInsert(
            ['id' => (string) $cart->id],
            [
                'items' => json_encode($cart->cartItems->map(fn(CartItem $item) => [
                    'id' => (string) $item->id,
                    'product' => [
                        'id' => (string) $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                    ],
                    'quantity' => $item->quantity,
                ])->values()->all()),
            ]
        );
    }

    public function delete(UuidVO $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
