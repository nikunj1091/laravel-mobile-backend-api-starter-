<?php

namespace App\Helpers;

use App\Constants\StatusCode;

class ApiResponse
{
    protected static bool $status  = true;
    protected static ?string $message = null;
    protected static mixed $data    = null;
    protected static mixed $errors  = null;
    protected static int $code;

    /**
     * Success response
     */
    public static function success(
        string $message = 'Success',
        mixed $data = null,
        int $code = StatusCode::OK
    ) {
        self::$status  = true;
        self::$message = $message;
        self::$data    = $data;
        self::$errors  = null;
        self::$code    = $code;

        return self::send();
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Something went wrong',
        mixed $errors = null,
        int $code = StatusCode::BAD_REQUEST
    ) {
        self::$status  = false;
        self::$message = $message;
        self::$data    = null;
        self::$errors  = $errors;
        self::$code    = $code;

        return self::send();
    }

    /**
     * Send JSON response
     */
    protected static function send()
    {
        return response()->json([
            'status'  => self::$status,
            'message' => self::$message,
            'data'    => self::$data,
            'errors'  => self::$errors,
        ], self::$code);
    }
}
