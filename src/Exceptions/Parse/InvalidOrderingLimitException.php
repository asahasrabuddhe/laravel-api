<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;
use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;

class InvalidOrderingDefinitionException extends BaseException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::ORDERING_INVALID;

    protected $message = "Ordering defined incorrectly";
}