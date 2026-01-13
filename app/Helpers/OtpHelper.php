<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (! function_exists('send_email_otp')) {

    function send_email_otp($user): array
    {
        try {
            $otp = random_int(100000, 999999);

            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::raw(
                "Your OTP is: {$otp}. It will expire in 10 minutes.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Email Verification OTP');
                }
            );

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
            ];

        } catch (\Throwable $e) {

            Log::error('OTP mail error', [
                'email' => $user->email ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Mail sending failed',
            ];
        }
    }
}
