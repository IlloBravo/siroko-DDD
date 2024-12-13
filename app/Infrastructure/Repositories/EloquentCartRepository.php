<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Product\Product;
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

        $cartItemsData = json_decode($cartData->items, true);

        $cartItems = collect($cartItemsData)->map(function ($item) {
            $productData = DB::table('products')->where('id', $item['product']['id'])->first();

            if (!$productData) {
                throw new ProductNotFoundException($item['product']['id']);
            }

            return CartItem::fromDatabase((object) [
                'id' => $item['id'],
                'cart' => null,
                'product' => Product::fromDatabase($productData),
                'quantity' => $item['quantity'],
            ]);
        });

        $cart = Cart::fromDatabase((object) [
            'id' => $cartData->id,
            'items' => json_encode($cartItems->toArray()),
        ]);

        foreach ($cart->getCartItems() as $cartItem) {
            $cartItem->cart = $cart;
        }

        return $cart;
    }

    public function findAll(): Collection
    {
        $cartsData = DB::table('carts')->get();

        return collect($cartsData)->map(
            fn($cartData) => $this->findByIdOrFail(UuidVO::fromString($cartData->id))
        );
    }

    public function save(Cart $cart): void
    {
        DB::table('carts')->updateOrInsert(
            ['id' => (string) $cart->id],
            [
                'items' => json_encode(
                    $cart->cartItems->map(fn(CartItem $item) => [
                        'id' => (string) $item->id,
                        'product' => [
                            'id' => (string) $item->product->id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                        ],
                        'quantity' => $item->quantity,
                    ])->toArray()
                ),
            ]
        );

        foreach ($cart->cartItems as $cartItem) {
            DB::table('cart_items')->updateOrInsert(
                ['id' => (string) $cartItem->id],
                [
                    'cart_id' => (string) $cartItem->cart->id,
                    'product_id' => (string) $cartItem->product->id,
                    'quantity' => $cartItem->quantity,
                ]
            );
        }
    }

    public function update(Cart $cart): void
    {
        DB::table('carts')->where('id', (string) $cart->id)->update([
            'items' => json_encode($cart->cartItems->map(fn(CartItem $item) => [
                'id' => (string) $item->id,
                'product' => [
                    'id' => (string) $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                ],
                'quantity' => $item->quantity,
            ])->toArray()),
        ]);

        foreach ($cart->cartItems as $cartItem) {
            DB::table('cart_items')->updateOrInsert(
                ['id' => (string) $cartItem->id],
                [
                    'cart_id' => (string) $cartItem->cart->id,
                    'product_id' => (string) $cartItem->product->id,
                    'quantity' => $cartItem->quantity,
                ]
            );
        }
    }

    public function delete(UuidVO $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
