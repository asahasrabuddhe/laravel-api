<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions;

use Illuminate\Http\Response;

class ResourceNotFoundException extends BaseException
{
    protected $statusCode = Response::HTTP_NOT_FOUND;

    protected $code = ErrorCodes::RESOURCE_NOT_FOUND_EXCEPTION;

    protected $message = "Requested resource not found";
}