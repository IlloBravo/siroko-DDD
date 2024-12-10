<?php

namespace App\Domain\Product\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(string $productId)
    {
        $message = __('Product.insufficient_stock', ['id' => $productId]);
        parent::__construct($message);
    }
}
