<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($message = 'Operation successful', $key = "data",  $data = [], $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
             $key    => $data,
        ], $status);
    }

    public static function error($message = 'Something went wrong', $status = 400, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    public static function validationError($errors): JsonResponse
    {
        return self::error(
            message: $errors ?? 'Validation failed',
            status: 422,
            errors: $errors
        );
    }

    public static function notFound($message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    public static function unauthorized($message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, 401);
    }
}