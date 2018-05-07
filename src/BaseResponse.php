<?php

namespace Asahasrabuddhe\LaravelAPI;

use Illuminate\Support\Facades\Response;
use Asahasrabuddhe\LaravelAPI\Exceptions\BaseException;

class BaseResponse
{
    /**
     * Make new success response.
     *
     * @param string $message
     * @param array $data
     * @return Response
     */
    public static function make($message = null, $data = null, $meta = null, $status = 200)
    {
        $response = [];

        if (! empty($message)) {
            $response['message'] = $message;
        }

        if ($data !== null && is_array($data)) {
            $response['data'] = $data;
        }

        if ($meta !== null && is_array($meta)) {
            $response['meta'] = $meta;
        }

        $returnResponse = Response::make($response, $status);

        return $returnResponse;
    }

    /**
     * Handle api exception an return proper error response.
     *
     * @param BaseException $exception
     * @return \Illuminate\Http\Response
     * @throws BaseException
     */
    public static function exception(BaseException $exception)
    {
        $returnResponse = Response::make($exception->jsonSerialize());

        $returnResponse->setStatusCode($exception->getStatusCode());

        return $returnResponse;
    }
}
