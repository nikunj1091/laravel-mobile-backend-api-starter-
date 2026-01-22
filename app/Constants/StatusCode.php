<?php

namespace App\Constants;

class StatusCode
{
    // Success
    public const OK = 200;
    public const CREATED = 201;

    // Client errors
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const UNPROCESSABLE_ENTITY = 422;
    public const TOO_MANY_REQUESTS = 429;

    // Server errors
    public const INTERNAL_SERVER_ERROR = 500;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        // Success
    }
}
