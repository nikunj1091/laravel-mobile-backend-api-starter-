<?php

namespace App\Services;

use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate 6 digit OTP
     */
    public function generate(): int
    {
        return random_int(100000, 999999);
    }

    /**
     * Save OTP to user
     */
    public function store(User $user, int $otp): void
    {
        $user->update([
            'otp_code'         => $otp,
            'otp_expires_at'   => now()->addMinutes(10),
            'last_otp_sent_at' => now(),
        ]);
    }

    /**
     * Send OTP mail
     */
    public function send(User $user, int $otp): void
    {
        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    /**
     * Generate + Store + Send OTP
     */
    public function generateAndSend(User $user): void
    {
        $otp = $this->generate();
        $this->store($user, $otp);
        $this->send($user, $otp);
    }

    /**
     * Validate OTP
     */
    public function isValid(User $user, string $otp): bool
    {
        return
            $user->otp_code &&
            $user->otp_code == $otp &&
            now()->lte($user->otp_expires_at);
    }

    /**
     * Clear OTP
     */
    public function clear(User $user): void
    {
        $user->update([
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);
    }
}
