<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\CartItem;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    /**
     * @throws DateMalformedStringException
     */
    public function findByIdOrFail(UuidVO $id): Cart
    {
        $uuid = UuidVO::fromString($id);

        $cartData = DB::table('carts')->where('id', $uuid)->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        return Cart::fromDatabase($cartData);
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
