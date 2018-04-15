<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;

class UnknownFieldException extends BaseException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::UNKNOWN_FIELD_EXCEPTION;

    protected $message = 'One of the specified fields does not exist';
}
