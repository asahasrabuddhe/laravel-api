<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;

class FilterNotFoundException extends BaseException
{
    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::FILTER_NOT_FOUND;

    protected $message = 'Requested filter cannot be found';
}
