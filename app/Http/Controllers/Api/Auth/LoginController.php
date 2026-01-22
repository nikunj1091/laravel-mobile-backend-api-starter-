<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\StatusCode;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error(
                'Invalid credentials',
                null,
                StatusCode::UNAUTHORIZED
            );
        }

        if (!$user->email_verified_at) {
            return ApiResponse::error(
                'Please verify your email first',
                null,
                StatusCode::FORBIDDEN
            );
        }

        // OPTIONAL: Single-device login
        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success(
            'Login successful',
            [
                'token' => $token,
                'user'  => $user
            ]
        );
    }

    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return ApiResponse::success(
            'Logged out successfully'
        );
    }
}
