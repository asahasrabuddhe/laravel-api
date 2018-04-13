<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;
use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;
use Illuminate\Http\Response;

class FieldCannotBeFilteredException extends BaseException
{
    protected $code = Response::HTTP_BAD_REQUEST;

    protected $innerError = ErrorCodes::INNER_NOT_ALLOWED_TO_FILTER_ON_THIS_FIELD;

    protected $message = "Applying filter on one of the specified fields is not allowed";
}