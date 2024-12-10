<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Cart\Cart;
use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Shared\ValueObjects\UuidVO;
use DateMalformedStringException;
use Illuminate\Support\Facades\DB;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function save(Cart $cart): void
    {
        DB::table('carts')->updateOrInsert(
            ['id' => $cart->id],
            [
                'items' => json_encode($cart->items),
                'created_at' => $cart->createdAt->format('Y-m-d H:i:s'),
                'updated_at' => $cart->updatedAt->format('Y-m-d H:i:s'),
            ]
        );
    }

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

    public function delete(string $id): void
    {
        DB::table('carts')->where('id', $id)->delete();
    }

    public function findAll(): array
    {
        $cartsData = DB::table('carts')->get();

        return $cartsData->map(function ($cartData) {
            return Cart::fromDatabase($cartData);
        })->toArray();
    }
}
