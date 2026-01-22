<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\StatusCode;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Forgot password – send OTP
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::error('User not found', null, StatusCode::NOT_FOUND);
        }

        // OTP resend cooldown
        if (
            $user->last_otp_sent_at &&
            now()->diffInSeconds($user->last_otp_sent_at) < 60
        ) {
            return ApiResponse::error(
                'Please wait before requesting another OTP',
                null,
                StatusCode::TOO_MANY_REQUESTS
            );
        }

        // Generate + store + send OTP via service
        $this->otpService->generateAndSend($user);

        // Reset forgot-password verification flag
        $user->update([
            'password_reset_verified_at' => null
        ]);

        return ApiResponse::success(
            'Password reset OTP sent to email'
        );
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->password_reset_verified_at) {
            return ApiResponse::error(
                'OTP verification required',
                null,
                StatusCode::FORBIDDEN
            );
        }

        $user->update([
            'password'                   => Hash::make($request->password),
            'password_reset_verified_at' => null
        ]);

        // Logout all devices
        $user->tokens()->delete();

        return ApiResponse::success(
            'Password reset successful'
        );
    }

    /**
     * Change password (logged-in user)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return ApiResponse::error(
                'Current password is incorrect',
                null,
                StatusCode::BAD_REQUEST
            );
        }

        if (Hash::check($request->password, $user->password)) {
            return ApiResponse::error(
                'New password cannot be same as old password',
                null,
                StatusCode::BAD_REQUEST
            );
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Logout other devices
        if ($user->currentAccessToken()) {
            $user->tokens()
                ->where('id', '!=', $user->currentAccessToken()->id)
                ->delete();
        }

        return ApiResponse::success(
            'Password changed successfully'
        );
    }
}
