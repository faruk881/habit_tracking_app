<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\MailVerifyRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\OtpUserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request) {
        try {
            $fields = $request->validated();

            $fields['password'] = Hash::make($fields['password']);

            $user = User::create($fields);

            $otpResult = send_email_otp($user);

            if (!$otpResult['success']) {

                // Optional rollback
                $user->delete();

                return response()->json([
                    'status'  => 0,
                    'message' => 'Failed to send OTP email',
                    'error'   => app()->isLocal()
                        ? $otpResult['error']   // FULL error (local/dev)
                        : null                  // hidden in production
                ], 500);
            }


            return apiSuccess('User created. OTP sent to email. Please verify your mail.',new OtpUserResource($user));

        } catch( \Throwable $e) {
            return apiError($e->getMessage(),500);
        }
    }

    public function login(UserLoginRequest $request) {

        try {
            $user = User::where('email', $request->email)->first();

            if(!$user->email_verified_at){

                if(!Carbon::now()->gt($user->otp_expires_at)){
                    send_email_otp($user);

                    return apiError('Hellow! '.$user->name .'. A otp is already sent to email. '.$user->email.'Please verify first to log in.',401);
                }

                if (send_email_otp($user)){
                    return apiError('Hellow! '.$user->name .'. A new otp sent to '.$user->email.'Please verify first to log in.', 401);
                }
            }


            if (! $user || ! Hash::check($request->password, $user->password)) {
                return apiError('Invalid credentials', 401);
            }

            if ($user->is_blocked) {
                return apiError('Your account is blocked', 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return apiSuccess('Login successful', [
                'name'  => $user->name,
                'email' => $user->email,
                'token' => $token,
            ]);
        } catch(\Throwable $e) {
            return apiError($e->getMessage(),500);
        }

    }

    public function mailVerify(MailVerifyRequest $request) {
        try{
            $user = User::where('email', $request->email)->first();

            if($user->email_verified_at){
                return apiError('The mail is already verified');
            }

            if ( !$user || !$user->otp || !Hash::check($request->otp, $user->otp) || Carbon::now()->gt($user->otp_expires_at)) {
                return apiError('Invalid or expired OTP, Try to log in to get new otp');
            }   

            $user->update([
                'otp' => null,
                'email_verified_at' => Carbon::now(),
            ]);

            return apiSuccess('email successfully verified, now you can log in',new OtpUserResource($user));
        } catch(\Throwable $e) {
            return apiError($e->getMessage());
        }

    }

    public function forgotPassword(ForgetPasswordRequest $request) {

        try {
            $user = User::where('email', $request->email)->first();

            if (! $user) {
                return apiError('If this email exists, an OTP has been sent');
            }

            $otp = rand(100000, 999999);

            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            // send email / SMS here
            // Mail::to($user->email)->send(new ResetOtpMail($otp));

            Mail::raw(
                "Your password reset OTP is {$otp}. It will expire in 10 minutes.",
                fn ($message) =>
                            $message->to($user->email)
                            ->subject('Password Reset OTP')
                );

            return apiSuccess('OTP sent to your email');
        } catch (\Throwable $e) {
            return apiError($e->getMessage());
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {

        try {
            $user = User::where('email', $request->email)->first();

            if (
                ! $user ||
                ! Hash::check($request->otp, $user->otp) ||
                $user->otp_expires_at < now()
            ) {
                return apiError('Invalid or expired OTP', 422);
            }

            $user->update([
                'password' => $request->password,
                'email_verified_at' => Carbon::now(),
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            // revoke all tokens
            $user->tokens()->delete();

            return apiSuccess('Password reset successful');
        } catch(\Throwable $e) {
            return apiError($e->getMessage());
        }
        
    }

}
