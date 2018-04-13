<?php

namespace Asahasrabuddhe\LaravelAPI\Exceptions;

class ErrorCodes
{
    const FILTER_NOT_FOUND = 1001;
    const INVALID_FILTER_DEFINITION = 1002;
    const UNKNOWN_FILED_EXCEPTION = 1003;
    const NOT_ALLOWED_TO_FILTER_ON_THIS_FIELD = 1004;
    const ORDERING_INVALID = 1005;
    const MAX_LIMIT = 1006;
    const INVALID_LIMIT = 1007;
    const RELATED_RESOURCE_NOT_EXISTS = 1010;
}