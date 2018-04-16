<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions;

use Illuminate\Http\Response;

class RelatedResourceNotFoundException extends BaseException
{
    protected $statusCode = Response::HTTP_FAILED_DEPENDENCY;

    protected $code = ErrorCodes::VALIDATION_EXCEPTION;

    protected $innercode = ErrorCodes::INNER_RELATED_RESOURCE_NOT_EXISTS;

    protected $message = 'Related resource not found';
}
