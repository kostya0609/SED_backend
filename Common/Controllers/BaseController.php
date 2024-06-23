<?php
namespace SED\Common\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    protected function sendResponse($result = null, string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if (!empty($result)) {
            $response['data'] = $result;
        }

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    protected function sendError(string $error = '', array $error_messages = []): JsonResponse
    {
        $response = [
            'success' => false,
        ];

        if (!empty($error)) {
            $response['message'] = $error;
        }

        if (!empty($error_messages)) {
            $response['messages'] = $error_messages;
        }

        return response()->json($response);
    }
}