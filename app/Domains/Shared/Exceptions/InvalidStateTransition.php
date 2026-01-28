<?php

namespace App\Domains\Shared\Exceptions;

use Exception;

class InvalidStateTransition extends Exception
{
    public function __construct(string $message = "Invalid state transition", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
