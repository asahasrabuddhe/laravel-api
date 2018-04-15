<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions;

class ErrorCodes
{
    const REQUEST_PARSE_EXCEPTION = 100;

    const UNKNOWN_EXCEPTION            = 1;
    const UNAUTHORIZED_EXCEPTION       = 401;
    const VALIDATION_EXCEPTION         = 422;
    const RESOURCE_NOT_FOUND_EXCEPTION = 404;

    const FILTER_NOT_FOUND                    = 1001;
    const INVALID_FILTER_DEFINITION           = 1002;
    const UNKNOWN_FIELD_EXCEPTION             = 1003;
    const NOT_ALLOWED_TO_FILTER_ON_THIS_FIELD = 1004;
    const ORDERING_INVALID                    = 1005;
    const MAX_LIMIT                           = 1006;
    const INVALID_LIMIT                       = 1007;
    const RELATED_RESOURCE_NOT_EXISTS         = 1010;
}
