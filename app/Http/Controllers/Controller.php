<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
abstract class Controller
{
    use AuthorizesRequests;
    protected function jsonResponse($result = true, $message = "", $code = 200, $data = null, $error = null)
    {
        $response = [
            'result' => $result,
            'status' => $code,
            'message' => $message,
        ];

        if ($data !== null || is_array($data)) {
            $response['data'] = $data;
        }

        if ($error) {
            $response['errors'] = $error;
        }

        return response()->json($response, $code);
    }
    public function FailResponse($data = [], $messages = ["Failed"], $status = 500, $custom_code = 500, $validation = false)
    {
        $errors = $messages;

        if ($validation) {
            $errors = [];
            foreach ($messages->errors() as $key => $messages) {
                $errors[] = $messages[0];
            }
        }

        return response()->json(
            [
                'code' => $custom_code != 500 ? (int)$custom_code : (int)$status,
                'messages' => is_string($errors) ? [$errors] : $errors,
                'data' => $data
            ],
            $status
        )->header('status', $status);
    }

    public function error_log($exception)
    {
        Log::error([
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage()
        ]);
    }

}
