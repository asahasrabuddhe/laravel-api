<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions;

use Illuminate\Http\Response;

class ValidationExcecption extends BaseException
{
    protected $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected $code = ErrorCodes::VALIDATION_EXCEPTION;

    protected $message = 'Request could not be validated';

    /**
     * Validation errors.
     *
     * @var array
     */
    private $errors = [];

    public function __construct($errors = [])
    {
        parent::__construct();

        $this->details = $errors;
    }
}
