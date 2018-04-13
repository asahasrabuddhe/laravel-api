<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;
use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;

class UnknownFieldException extends BaseException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INNER_UNKNOWN_FILED_EXCEPTION;
    
    protected $message = "One of the specified fields does not exist";
}