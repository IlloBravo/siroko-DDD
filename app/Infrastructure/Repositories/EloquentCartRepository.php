<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use DateMalformedStringException;
use DateTime;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function save(Cart $cart): void
    {
        DB::table('carts')->updateOrInsert(
            ['id' => $cart->id],
            [
                'items' => json_encode($cart->items),
                'created_at' => $cart->createdAt,
                'updated_at' => $cart->updatedAt,
            ]
        );
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findByIdOrFail(string $id): Cart
    {
        $cartData = DB::table('carts')->where('id', $id)->first();

        if (!$cartData) {
            throw new CartNotFoundException($id);
        }

        $items = collect(json_decode($cartData->items, true))->map(function (array $item): Product {
            return Product::fromArray([
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        });

        return Cart::fromArray(
            $cartData->id,
            $items,
            $cartData->created_at,
            $cartData->updated_at
        );
    }

    public function delete(string $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
