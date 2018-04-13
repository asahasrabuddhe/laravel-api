<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;
use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;

class FilterNotFoundException extends BaseException
{

    protected $code = ErrorCodes::REQUEST_PARSE_EXCEPTION;

    protected $innerError = ErrorCodes::INNER_FILTER_NOT_FOUND;

    protected $message = "Requested filter cannot be found";

}