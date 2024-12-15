<?php

namespace App\Domain\Product\Exceptions;

use Illuminate\Support\Facades\Lang;
use RuntimeException;

final class InsufficientStockException extends RuntimeException
{
    public function __construct(string $productName, int $stock)
    {
        $message = Lang::get('Product.insufficient_stock',
            [
                'name' => $productName,
                'stock' => $stock
            ]
        );

        parent::__construct($message);
    }
}
