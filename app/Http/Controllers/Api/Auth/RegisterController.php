<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\StatusCode;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class RegisterController
 *
 * Handles user registration for API authentication.
 * - Creates a new user
 * - Generates and sends OTP via email
 * - Does NOT log the user in directly
 *
 * API Version: v1
 */
class RegisterController extends Controller
{
    /**
     * OTP service instance
     *
     * @var OtpService
     */
    protected OtpService $otpService;

    /**
     * RegisterController constructor.
     *
     * OtpService is injected via Laravel service container
     * to keep OTP logic reusable and controller clean.
     *
     * @param OtpService $otpService
     */
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register a new user and send OTP to email.
     *
     * Flow:
     * 1. Validate request data
     * 2. Create user with hashed password
     * 3. Generate & send OTP using OtpService
     * 4. Return standardized API response
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Create new user record
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate, store and send OTP (email verification)
        // All OTP-related logic is handled inside OtpService
        $this->otpService->generateAndSend($user);

        // Return standardized success response
        return ApiResponse::success(
            'Registered successfully. Please check your email for the OTP code.',
            [
                'email' => $user->email
            ],
            StatusCode::CREATED
        );
    }
}
