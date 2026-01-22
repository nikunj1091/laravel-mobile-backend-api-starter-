<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Versioned API routes for authentication
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\Auth\ProfileController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Authentication Routes (No Auth Required)
    |--------------------------------------------------------------------------
    */

    // Register user and send OTP
    Route::post('/register', [RegisterController::class, 'register']);

    // Verify email OTP
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);

    // Resend OTP
    Route::post('/resend-otp', [OtpController::class, 'resendOtp']);

    // Login (email must be verified)
    Route::post('/login', [LoginController::class, 'login']);

    // Forgot password – send OTP
    Route::post('/forgot-password', [PasswordController::class, 'forgotPassword']);

    // Verify forgot password OTP
    Route::post('/verify-forgot-otp', [OtpController::class, 'verifyForgotOtp']);

    // Reset password
    Route::post('/reset-password', [PasswordController::class, 'resetPassword']);


    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Sanctum Required)
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // Get authenticated user profile
        Route::get('/profile', [ProfileController::class, 'profile']);

        // Change password
        Route::post('/change-password', [PasswordController::class, 'changePassword']);

        // Logout
        Route::post('/logout', [LoginController::class, 'logout']);
    });
});
