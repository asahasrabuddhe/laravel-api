<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;

class InvalidOrderingLimitException extends BaseException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::ORDERING_INVALID;

    protected $message = 'Ordering defined incorrectly';
}
