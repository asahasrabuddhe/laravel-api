<?php

namespace Asahasrabuddhe\LaravelAPI\Middleware;

use Closure;
use Illuminate\Support\Str;
use Asahasrabuddhe\LaravelAPI\BaseResponse;
use Asahasrabuddhe\LaravelAPI\Exceptions\UnauthorizedException;

class BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        // Add CORS headers
        $response = $next($request);

        if ($response->getStatusCode() == 403 && ($response->getContent() == 'Forbidden' || Str::contains($response->getContent(), ['HttpException', 'authorized']))) {
            $response = BaseResponse::exception(new UnauthorizedException());
        }

        if (config('api.allowCors') && ! $response instanceof StreamedResponse) {
            $response->header('Access-Control-Allow-Origin', implode(',', config('api.allowedCorsOrigins')))
                ->header('Access-Control-Allow-Methods', implode(',', config('api.allowedCorsMethods')))
                ->header('Access-Control-Allow-Headers', implode(',', config('api.allowedCorsHeaders')));
        }

        return $response;
    }
}
