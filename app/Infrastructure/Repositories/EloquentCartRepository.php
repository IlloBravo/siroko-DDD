<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
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
    public function findByIdOrFail(string $id): ?Cart
    {
        $cartData = DB::table('carts')->where('id', $id)->first();

        if (!$cartData) {
            return null;
        }

        $items = collect(json_decode($cartData->items, true))->map(function ($item) {
            return new Product(
                $item['id'],
                $item['name'],
                $item['price'],
                $item['quantity']
            );
        });

        return new Cart(
            $cartData->id,
            $items,
            new DateTime($cartData->created_at),
            new DateTime($cartData->updated_at)
        );
    }

    public function delete(string $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }
}
