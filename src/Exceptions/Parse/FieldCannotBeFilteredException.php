<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions\Parse;

use Illuminate\Http\Response;
use Asahasrabuddhe\LaravelAPI\Exceptions\ErrorCodes;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;

class FieldCannotBeFilteredException extends BaseException
{
    protected $code = Response::HTTP_BAD_REQUEST;

    protected $innerError = ErrorCodes::NOT_ALLOWED_TO_FILTER_ON_THIS_FIELD;

    protected $message = 'Applying filter on one of the specified fields is not allowed';
}
