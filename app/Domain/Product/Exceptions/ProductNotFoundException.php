<?php

namespace App\Domain\Product\Exceptions;

use RuntimeException;

class ProductNotFoundException extends RuntimeException
{
    public function __construct(string $productId)
    {
        $message = __('product.product_not_found', ['id' => $productId]);
        parent::__construct($message);
    }
}
