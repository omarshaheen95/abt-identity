<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sendResponse($data, $message = 'success', $code = 200)
    {
        $response = [
            'success' => true,
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response, 200);
    }

    protected function sendError($error, $code = 200, $errorMessages = [])
    {
        $response = [
            'success' => false,
            'status' => $code,
            'message' => $error,
        ];

        $response['errors'] = $errorMessages;

        return response()->json($response, $code);
    }
}
