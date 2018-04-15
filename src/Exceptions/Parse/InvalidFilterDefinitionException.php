<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;

class InvalidFilterDefinitionException extends BaseException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INVALID_FILTER_DEFINITION;

    protected $message = 'Filter has been defined incorrectly';
}
