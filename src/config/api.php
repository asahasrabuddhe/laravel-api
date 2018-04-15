<?php

/*
 * API Configuration Options
 */
return [
    /*
     * Number of records to return per request when a limit is not specified
     */
    'perPage' => 10,
    /*
     * Allow Cross Origin Resouce Sharing. It is recommended to allow CORS requests
     * but you can disable this option.
     */
    'allowCors' => true,
    /*
     * The following list of headers will be permitted for a CORS request.
     */
    'allowedCorsHeaders' => [
        'Authorization',
        'Content-Type',
    ],
    /*
     * The following list of hosts will be permitted for a CORS request.
     */
    'allowedCorsOrigins' => [
        // Your Host Name Here
    ],
    /*
     * The following list of HTTP Requests will be permitted for a CORS request.
     */
    'allowedCorsMethods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],
    /*
     * The global prefix for all API routes.
     */
    'prefix' => 'api',
    /*
     * The current version of the API.
     */
    'version' => 'v1',
    /*
     * The older version(s) of the API that are still supported.
     */
    // 'supportedVersions' => [
    //    'vOld',
    //  ],
    /*
     * The older version(s) of the API that are deprecated and are not available for use.
     */
    // 'deprecatedVersions' => [
    //    'vOlder',
    //  ],
];
