<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send a success response.
     */
    protected function successResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response.
     */
    protected function errorResponse(string $message, int $code, $data = null): JsonResponse
    {
        return response()->json([
            'status' => 'Failed',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
