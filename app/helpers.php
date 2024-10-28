<?php

use Illuminate\Http\JsonResponse;

function apiResponse($message, array|object $data = [], array|object $errors = [], $status = 200): JsonResponse
{
    return response()->json([
        'data' => $data,
        'errors' => $errors,
        'status' => $status,
        'message' => $message
    ], $status);
}
