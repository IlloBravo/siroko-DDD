<?php

namespace App\Domain\Shared\Exceptions;

use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

final class InvalidQuantityException extends InvalidArgumentException
{
    public function __construct(string $value)
    {
        $message  = Lang::get('Shared.invalid_quantity', ['value' => $value]);
        parent::__construct($message);
    }
}