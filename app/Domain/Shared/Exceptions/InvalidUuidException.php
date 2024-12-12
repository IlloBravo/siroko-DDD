<?php

namespace App\Domain\Shared\Exceptions;

use Illuminate\Support\Facades\Lang;
use InvalidArgumentException;

class InvalidUuidException extends InvalidArgumentException
{
    public function __construct(string $value)
    {
        $message  = Lang::get('Uuid.invalid_uuid', ['value' => $value]);
        parent::__construct($message);
    }
}