<?php

namespace App\Domain\Product\Exceptions;

use Illuminate\Support\Facades\Lang;
use RuntimeException;

final class InsufficientStockException extends RuntimeException
{
    public function __construct(string $productId)
    {
        $message = Lang::get('Product.insufficient_stock', ['id' => $productId]);
        parent::__construct($message);
    }
}
