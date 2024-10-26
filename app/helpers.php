<?php

use Illuminate\Http\JsonResponse;

function apiResponse($status, $message, $data = null): JsonResponse
{
    return response()->json([
        'data' => $data,
        'status' => $status,
        'message' => $message
    ]);
}
