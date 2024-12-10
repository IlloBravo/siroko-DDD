<?php

namespace App\Domain\Shared\Exceptions;

use InvalidArgumentException;

class InvalidUuidException extends InvalidArgumentException
{
    public function __construct(string $value)
    {
        $message  = __('Uuid.invalid_uuid', ['value' => $value]);
        parent::__construct($message);
    }
}
