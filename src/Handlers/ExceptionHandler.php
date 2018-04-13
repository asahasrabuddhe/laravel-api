<?php

namespace Asahasrabuddhe\LaravelAPI\Handlers;

use App\Exceptions\Handler;
use Asahasrabuddhe\LaravelAPI\Response;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;
use Asahasrabuddhe\LaravelAPI\Exceptions\Parse\UnknownFieldException;
use Asahasrabuddhe\LaravelAPI\Exceptions\UnauthorizedException;
use Asahasrabuddhe\LaravelAPI\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exception\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler extends Handler
{

    public function render($request, \Exception $e)
    {
        $debug = env("APP_DEBUG");

        if (!$debug) {
            if ($e instanceof HttpResponseException || $e instanceof \Illuminate\Validation\ValidationException) {
                if ($e->getResponse()->getStatusCode() == 403) {
                    return Response::exception(new UnauthorizedException());
                }
                else {
                    return Response::exception(new ValidationException(json_decode($e->getResponse()->getContent(), true)));
                }
            }
            else if ($e instanceof NotFoundHttpException) {
                return Response::exception(new BaseException('This api endpoint does not exist', null, 404, 404, 2005, [
                        'url' => request()->url()
                    ]));
            }
            else if ($e instanceof ModelNotFoundException) {
                return Response::exception(new BaseException('Requested resource not found', null, 404, 404, null, [
                        'url' => request()->url()
                    ]));
            }
            else if ($e instanceof Exception) {
                return Response::exception($e);
            }
            else if ($e instanceof QueryException) {
                if ($e->getCode() == "42S22") {
                    preg_match("/Unknown column \\'([^']+)\\'/", $e->getMessage(), $result);

                    if (!isset($result[1])) {
                        return Response::exception(new UnknownFieldException(null, $e));
                    }
                    else {
                        $parts = explode(".", $result[1]);

                        if (count($parts) > 1) {
                            return Response::exception(new UnknownFieldException("Field '" . $parts[1] . "' does not exist", $e));
                        }
                        else {
                            return Response::exception(new UnknownFieldException("Field '" . $result . "' does not exist", $e));
                        }
                    }
                }
                else {
                    return Response::exception(new BaseException(null, $e));
                }
            }
            else {
                return Response::exception(new BaseException(null, $e));
            }
        }
        else {
            return parent::render($request, $e);
        }

    }
}