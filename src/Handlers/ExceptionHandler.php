<?php

namespace Asahasrabuddhe\LaravelAPI\Handlers;

use App\Exceptions\Handler;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exception\HttpResponseException;
use Asahasrabuddhe\LaravelAPI\BaseResponse as Response;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Asahasrabuddhe\LaravelAPI\Exceptions\ValidationException;
use Asahasrabuddhe\LaravelAPI\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\UnknownFieldException;

class ExceptionHandler extends Handler
{
    public function render($request, \Exception $e)
    {
        $debug = env('APP_DEBUG');

        if (! $debug) {
            if ($e instanceof HttpResponseException || $e instanceof \Illuminate\Validation\ValidationException) {
                if ($e->getResponse()->getStatusCode() == 403) {
                    return Response::exception(new UnauthorizedException());
                }

                return Response::exception(new ValidationException(json_decode($e->getResponse()->getContent(), true)));
            } elseif ($e instanceof NotFoundHttpException) {
                return Response::exception(new BaseException('This api endpoint does not exist', null, 404, 404, 2005, [
                        'url' => request()->url(),
                    ]));
            } elseif ($e instanceof ModelNotFoundException) {
                return Response::exception(new BaseException('Requested resource not found', null, 404, 404, null, [
                        'url' => request()->url(),
                    ]));
            } elseif ($e instanceof Exception) {
                return Response::exception($e);
            } elseif ($e instanceof QueryException) {
                if ($e->getCode() == '42S22') {
                    preg_match("/Unknown column \\'([^']+)\\'/", $e->getMessage(), $result);

                    if (! isset($result[1])) {
                        return Response::exception(new UnknownFieldException(null, $e));
                    }

                    $parts = explode('.', $result[1]);

                    if (count($parts) > 1) {
                        return Response::exception(new UnknownFieldException("Field '" . $parts[1] . "' does not exist", $e));
                    }

                    return Response::exception(new UnknownFieldException("Field '" . $result . "' does not exist", $e));
                }

                return Response::exception(new BaseException(null, $e));
            }

            return Response::exception(new BaseException(null, $e));
        }

        return parent::render($request, $e);
    }
}
