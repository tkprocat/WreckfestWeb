<?php

namespace App\Exceptions;

use Exception;

class WreckfestApiException extends Exception
{
    public function __construct(string $message = 'Unable to contact Wreckfest Controller', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
