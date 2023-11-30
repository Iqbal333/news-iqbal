<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function success($code = 200, $message = null, $data = null)
    {
        $response = [
            'status'  => true,
            'code'    => $code,
            'message' => $message,
        ];

        if($data !== null) {
            $response['results'] = $data;
        }

        return response()->json($response, $code);
    }

    public function error($code = 500, $message = null, $errors, $exception = null)
    {
        $debug = config('app.debug');

        $response = [
            'status'   => false,
            'code'     => $code,
            'messages' => $message,
            'errors'   => $errors
        ];

        if($debug && $exception !== null) {
            $response['exception']['from'] = get_class($exception);
            $response['exception']['trace'] = explode("\n", $exception->getTraceAsString());
        }

        return response()->json($response, $code);
    }
}
