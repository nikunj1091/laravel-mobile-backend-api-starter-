<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\StatusCode;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Verify email OTP (register)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::error('User not found', null, StatusCode::NOT_FOUND);
        }

        if (!$this->otpService->isValid($user, $request->otp)) {
            return ApiResponse::error('Invalid or expired OTP', null, StatusCode::BAD_REQUEST);
        }

        $user->update([
            'email_verified_at' => now(),
        ]);

        $this->otpService->clear($user);

        return ApiResponse::success('Email verified successfully');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::error('User not found', null, StatusCode::NOT_FOUND);
        }

        if ($user->email_verified_at) {
            return ApiResponse::error('Email already verified', null, StatusCode::BAD_REQUEST);
        }

        if (
            $user->last_otp_sent_at &&
            $user->last_otp_sent_at->addSeconds(60)->isFuture()
        ) {
            return ApiResponse::error(
                'Please wait before requesting another OTP',
                null,
                StatusCode::TOO_MANY_REQUESTS
            );
        }

        $this->otpService->generateAndSend($user);

        return ApiResponse::success('OTP resent successfully');
    }

    /**
     * Verify OTP for forgot password
     */
    public function verifyForgotOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->otp_code) {
            return ApiResponse::error('Invalid request', null, StatusCode::BAD_REQUEST);
        }

        if (!$this->otpService->isValid($user, $request->otp)) {
            return ApiResponse::error('Invalid or expired OTP', null, StatusCode::BAD_REQUEST);
        }

        $user->update([
            'password_reset_verified_at' => now(),
        ]);

        $this->otpService->clear($user);

        return ApiResponse::success(
            'OTP verified, you can reset password now'
        );
    }
}
