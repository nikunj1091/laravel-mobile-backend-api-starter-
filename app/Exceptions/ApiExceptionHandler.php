<?php

namespace App\Exceptions;

use App\Constants\StatusCode;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiExceptionHandler
{
    public static function register($exceptions)
    {
        // Validation errors (API only)
        $exceptions->render(function (ValidationException $e, $request) {
            if (!self::isApiRequest($request)) {
                return null; // let Laravel handle web
            }

            return ApiResponse::error(
                'Validation error',
                $e->errors(),
                StatusCode::UNPROCESSABLE_ENTITY
            );
        });

        // Unauthenticated (API only)
        $exceptions->render(function (AuthenticationException $e, $request) {
            if (!self::isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                'Unauthenticated',
                null,
                StatusCode::UNAUTHORIZED
            );
        });

        // 404 Model not found (API only)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if (!self::isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                'Resource not found',
                null,
                StatusCode::NOT_FOUND
            );
        });

        // HTTP exceptions (API only)
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if (!self::isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                $e->getMessage() ?: 'Request error',
                null,
                $e->getStatusCode()
            );
        });
    }

    protected static function isApiRequest($request): bool
    {
        return $request->expectsJson()
            || $request->is('api/*');
    }
}
