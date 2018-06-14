<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedException extends BaseException
{
    protected $statusCode = Response::HTTP_UNAUTHORIZED;

    protected $code = ErrorCodes::UNAUTHORIZED_EXCEPTION;

    protected $message = 'Not authorized to perform this request';
}
