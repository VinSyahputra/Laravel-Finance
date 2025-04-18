<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * return success response.
     *
     * @return \Illuminate\Http\Response
     */
    public function responseSuccess($result = [], $message = 'Task done', $code = Response::HTTP_OK)
    {

        $response = [
            "code" => $code,
            "status" => "SUCCESS",
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, $code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function responseError($errorMessages = [], $message = 'Something happen', $code = Response::HTTP_BAD_REQUEST)
    {

        $response = [
            "code" => $code,
            "status" => "FAILED",
            'message' => $message,
        ];

        if (!empty($errorMessages)) $response['data'] = $errorMessages;

        return response()->json($response, $code);
    }
}
