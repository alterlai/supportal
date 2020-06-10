<?php

namespace App\Exception;

use Exception;
use Throwable;

class InvalidReportGroupingException extends Exception implements CustomExceptionsInterface
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}